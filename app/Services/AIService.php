<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Behavior;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Collection;

/**
 * خدمة الذكاء الاصطناعي والتحليلات الذكية
 * تعمل على بيانات المنصة مباشرة دون الحاجة لمفاتيح API خارجية
 */
class AIService
{
    /**
     * توليد رؤى ذكية لطالب معين
     */
    public function studentInsights(Student $student): array
    {
        $scores = $student->scores()->with('subject')->get();
        $attendances = $student->attendances()->get();
        $behaviors = $student->behaviors()->get();

        $insights = [
            'summary' => $this->studentSummary($student, $scores, $attendances),
            'performance_trend' => $this->performanceTrend($scores),
            'risk_assessment' => $this->assessRisk($student, $scores, $attendances),
            'recommendations' => $this->getRecommendations($student, $scores, $attendances, $behaviors),
            'strengths' => $this->studentStrengths($student, $scores),
            'weaknesses' => $this->studentWeaknesses($student, $scores),
            'attendance_analysis' => $this->analyzeAttendance($attendances),
            'behavior_analysis' => $this->analyzeBehavior($behaviors),
        ];

        return $insights;
    }

    /**
     * ملخص الطالب
     */
    protected function studentSummary(Student $student, Collection $scores, Collection $attendances): string
    {
        $avgScore = $scores->avg('score');
        $attendanceRate = $attendances->isNotEmpty()
            ? round(($attendances->where('status', 'present')->count() / $attendances->count()) * 100)
            : 100;

        $performance = match(true) {
            $avgScore >= 85 => 'ممتازة',
            $avgScore >= 70 => 'جيدة',
            $avgScore >= 50 => 'تحتاج إلى تحسين',
            default => 'ضعيفة',
        };

        $attendanceState = $attendanceRate >= 90 ? 'الحضور منتظم' : ($attendanceRate >= 75 ? 'الحضور متوسط' : 'الحضور غير منتظم');

        return "الطالب {$student->name} لديه أداء {$performance} بمتوسط درجات {$avgScore}، و{$attendanceState} بنسبة {$attendanceRate}%.";
    }

    /**
     * تحليل اتجاه الأداء عبر الأشهر والترمات
     */
    protected function performanceTrend(Collection $scores): array
    {
        if ($scores->isEmpty()) {
            return ['trend' => 'لا توجد بيانات كافية', 'direction' => 'neutral', 'data' => []];
        }

        $monthly = $scores
            ->whereNotNull('month')
            ->groupBy('term')
            ->map(function ($termScores) {
                return $termScores->groupBy('month')->map(fn($m) => round($m->avg('score'), 2));
            });

        $allScores = $scores->sortBy('created_at')->pluck('score')->values();
        $direction = 'neutral';

        if (count($allScores) >= 2) {
            $first = $allScores->first();
            $last = $allScores->last();
            if ($last > $first) $direction = 'up';
            elseif ($last < $first) $direction = 'down';
        }

        return [
            'trend' => match($direction) {
                'up' => 'الأداء في تحسن مستمر',
                'down' => 'الأداء في انخفاض، يحتاج إلى تدخل',
                default => 'الأداء مستقر',
            },
            'direction' => $direction,
            'data' => $monthly->toArray(),
        ];
    }

    /**
     * تقييم خطر التعثر الأكاديمي
     */
    protected function assessRisk(Student $student, Collection $scores, Collection $attendances): array
    {
        if ($scores->isEmpty()) {
            return ['level' => 'unknown', 'label' => 'غير محدد', 'score' => 0, 'factors' => []];
        }

        $riskScore = 0;
        $factors = [];

        // متوسط الدرجات
        $avgScore = $scores->avg('score');
        if ($avgScore < 50) {
            $riskScore += 40;
            $factors[] = 'متوسط الدرجات منخفض جداً';
        } elseif ($avgScore < 65) {
            $riskScore += 25;
            $factors[] = 'متوسط الدرجات أقل من المتوسط';
        } elseif ($avgScore < 75) {
            $riskScore += 10;
        }

        // اتجاه الأداء
        if ($scores->count() >= 3) {
            $first = $scores->sortBy('created_at')->first()->score;
            $last = $scores->sortBy('created_at')->last()->score;
            if ($last < $first - 15) {
                $riskScore += 20;
                $factors[] = 'انخفاض ملحوظ في الأداء مؤخراً';
            }
        }

        // الحضور
        if ($attendances->isNotEmpty()) {
            $absentRate = $attendances->whereIn('status', ['absent', 'late'])->count() / $attendances->count();
            if ($absentRate > 0.3) {
                $riskScore += 25;
                $factors[] = 'نسبة الغياب والتأخير مرتفعة';
            } elseif ($absentRate > 0.15) {
                $riskScore += 10;
                $factors[] = 'نسبة الغياب والتأخير متوسطة';
            }
        }

        // السلوك
        $negativeBehaviors = $student->behaviors()->where('type', 'negative')->count();
        if ($negativeBehaviors >= 3) {
            $riskScore += 15;
            $factors[] = 'وجود سلوكيات سلبية متكررة';
        }

        $level = match(true) {
            $riskScore >= 50 => 'high',
            $riskScore >= 25 => 'medium',
            $riskScore > 0 => 'low',
            default => 'none',
        };

        return [
            'level' => $level,
            'label' => match($level) {
                'high' => 'خطر مرتفع - يحتاج تدخل فوري',
                'medium' => 'خطر متوسط - يحتاج متابعة',
                'low' => 'خطر منخفض - يحتاج مراقبة',
                'none' => 'لا يوجد خطر - أداء جيد',
                default => 'غير محدد',
            },
            'score' => $riskScore,
            'factors' => $factors,
        ];
    }

    /**
     * توليد توصيات ذكية
     */
    protected function getRecommendations(Student $student, Collection $scores, Collection $attendances, Collection $behaviors): array
    {
        $recommendations = [];

        // توصيات الدرجات
        $weakSubjects = $scores->groupBy('subject_id')->filter(fn($s) => $s->avg('score') < 60);
        foreach ($weakSubjects as $subjectId => $subjectScores) {
            $subject = $subjectScores->first()->subject;
            $avg = round($subjectScores->avg('score'), 2);
            $recommendations[] = [
                'type' => 'academic',
                'icon' => 'fa-book',
                'color' => 'red',
                'title' => "تحسين مادة: {$subject->name}",
                'text' => "متوسط الدرجات في {$subject->name} هو {$avg}. يُنصح بمراجعة المنهج وتقديم تمارين إضافية ودعم أكاديمي.",
                'priority' => $avg < 50 ? 'high' : 'medium',
            ];
        }

        // توصيات الحضور
        if ($attendances->isNotEmpty()) {
            $absentCount = $attendances->where('status', 'absent')->count();
            $lateCount = $attendances->where('status', 'late')->count();
            if ($absentCount >= 3) {
                $recommendations[] = [
                    'type' => 'attendance',
                    'icon' => 'fa-calendar-times',
                    'color' => 'amber',
                    'title' => 'معالجة الغياب المتكرر',
                    'text' => "الطالب غائب {$absentCount} مرة. يُنصح بالتواصل مع ولي الأمر ومعالجة أسباب الغياب.",
                    'priority' => 'high',
                ];
            }
            if ($lateCount >= 3) {
                $recommendations[] = [
                    'type' => 'attendance',
                    'icon' => 'fa-clock',
                    'color' => 'yellow',
                    'title' => 'معالجة التأخير المتكرر',
                    'text' => "الطالب متأخر {$lateCount} مرة. يُنصح بمتابعة مواعيد الوصول المبكر.",
                    'priority' => 'medium',
                ];
            }
        }

        // توصيات السلوك
        $negativeBehaviors = $behaviors->where('type', 'negative');
        if ($negativeBehaviors->count() >= 2) {
            $recommendations[] = [
                'type' => 'behavior',
                'icon' => 'fa-frown',
                'color' => 'purple',
                'title' => 'تدعيم إيجابي للسلوك',
                'text' => 'يوجد سلوكيات سلبية متكررة. يُنصح بتطبيق خطة تدعيم إيجابي ومكافآت لتحسين السلوك.',
                'priority' => 'medium',
            ];
        }

        // توصية إيجابية
        $positiveBehaviors = $behaviors->where('type', 'positive');
        if ($positiveBehaviors->count() >= 2) {
            $recommendations[] = [
                'type' => 'positive',
                'icon' => 'fa-star',
                'color' => 'green',
                'title' => 'تشجيع السلوك المتميز',
                'text' => 'الطالب يظهر سلوكاً إيجابياً مميزاً. يُنصح بتعزيز ذلك وتقديم مكافآت تشجيعية.',
                'priority' => 'low',
            ];
        }

        // توصية عامة
        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'general',
                'icon' => 'fa-check-circle',
                'color' => 'green',
                'title' => 'أداء جيد',
                'text' => 'الطالب في حالة جيدة. استمر في المتابعة والتحفيز للحفاظ على الأداء.',
                'priority' => 'low',
            ];
        }

        // ترتيب حسب الأولوية
        $priorityOrder = ['high' => 0, 'medium' => 1, 'low' => 2];
        usort($recommendations, function ($a, $b) use ($priorityOrder) {
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });

        return $recommendations;
    }

    /**
     * نقاط القوة
     */
    protected function studentStrengths(Student $student, Collection $scores): array
    {
        $strengths = [];
        $strongSubjects = $scores->groupBy('subject_id')->filter(fn($s) => $s->avg('score') >= 80);
        foreach ($strongSubjects as $subjectId => $subjectScores) {
            $subject = $subjectScores->first()->subject;
            $strengths[] = [
                'name' => $subject->name,
                'average' => round($subjectScores->avg('score'), 2),
            ];
        }
        return $strengths;
    }

    /**
     * نقاط الضعف
     */
    protected function studentWeaknesses(Student $student, Collection $scores): array
    {
        $weaknesses = [];
        $weakSubjects = $scores->groupBy('subject_id')->filter(fn($s) => $s->avg('score') < 70);
        foreach ($weakSubjects as $subjectId => $subjectScores) {
            $subject = $subjectScores->first()->subject;
            $weaknesses[] = [
                'name' => $subject->name,
                'average' => round($subjectScores->avg('score'), 2),
            ];
        }
        return $weaknesses;
    }

    /**
     * تحليل الحضور
     */
    protected function analyzeAttendance(Collection $attendances): array
    {
        if ($attendances->isEmpty()) {
            return ['rate' => 100, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'status' => 'لا توجد بيانات'];
        }

        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $excused = $attendances->where('status', 'excused')->count();
        $total = $attendances->count();
        $rate = round(($present / $total) * 100, 2);

        $status = match(true) {
            $rate >= 90 => 'ممتاز',
            $rate >= 75 => 'جيد',
            $rate >= 60 => 'متوسط',
            default => 'ضعيف',
        };

        return compact('rate', 'present', 'absent', 'late', 'excused', 'status');
    }

    /**
     * تحليل السلوك
     */
    protected function analyzeBehavior(Collection $behaviors): array
    {
        if ($behaviors->isEmpty()) {
            return ['positive' => 0, 'negative' => 0, 'total_points' => 0, 'status' => 'لا توجد بيانات'];
        }

        $positive = $behaviors->where('type', 'positive')->count();
        $negative = $behaviors->where('type', 'negative')->count();
        $totalPoints = $behaviors->sum('points');

        $status = match(true) {
            $positive > $negative => 'سلوك إيجابي',
            $positive === $negative && $positive > 0 => 'سلوك متوازن',
            $negative > 0 => 'يحتاج إلى تحسين',
            default => 'لا يوجد تقييم',
        };

        return compact('positive', 'negative', 'total_points', 'status');
    }

    /**
     * تحليلات عامة للمدرسة (لوحة التحكم)
     */
    public function schoolAnalytics(): array
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalClassrooms = Classroom::count();
        $totalGrades = Grade::count();
        $totalSubjects = Subject::count();

        // متوسط الدرجات العام
        $avgScore = Score::avg('score');

        // توزيع الحضور
        $attendanceStats = [
            'present' => Attendance::where('status', 'present')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'late' => Attendance::where('status', 'late')->count(),
            'excused' => Attendance::where('status', 'excused')->count(),
        ];

        // أداء الصفوف
        $gradePerformance = Grade::with('students')
            ->get()
            ->map(function ($grade) {
                $studentIds = $grade->students->pluck('id');
                $avg = Score::whereIn('student_id', $studentIds)->avg('score');
                return [
                    'name' => $grade->name,
                    'average' => round($avg ?? 0, 2),
                    'students_count' => $studentIds->count(),
                ];
            })
            ->filter(fn($g) => $g['students_count'] > 0)
            ->values();

        // توزيع المخاطر
        $riskDistribution = ['high' => 0, 'medium' => 0, 'low' => 0, 'none' => 0];
        foreach (Student::with('scores')->get() as $student) {
            $risk = $this->assessRisk($student, $student->scores, $student->attendances()->get())['level'];
            if (in_array($risk, ['high', 'medium', 'low', 'none'])) {
                $riskDistribution[$risk]++;
            }
        }

        // أفضل الطلاب
        $topStudents = Student::with('scores')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'average' => round($student->scores->avg('score') ?? 0, 2),
                ];
            })
            ->filter(fn($s) => $s['average'] > 0)
            ->sortByDesc('average')
            ->take(10)
            ->values();

        return [
            'totals' => compact('totalStudents', 'totalTeachers', 'totalClassrooms', 'totalGrades', 'totalSubjects'),
            'avgScore' => round($avgScore ?? 0, 2),
            'attendance' => $attendanceStats,
            'grade_performance' => $gradePerformance,
            'risk_distribution' => $riskDistribution,
            'top_students' => $topStudents,
        ];
    }

    /**
     * المساعد الذكي - فهم الأسئلة والرد
     */
    public function assistant(string $question, string $role, ?Student $contextStudent = null): array
    {
        $question = mb_strtolower(trim($question));
        $responses = [];

        // قوالب الرؤى
        $quiz = $this->buildAssistantKnowledge($role, $contextStudent);

        // البحث عن إجابة
        $answer = $this->matchQuestion($question, $quiz);

        return [
            'question' => $question,
            'answer' => $answer['text'],
            'type' => $answer['type'],
            'data' => $answer['data'] ?? null,
            'suggestions' => $this->getSuggestions($role),
        ];
    }

    /**
     * بناء معرفة المساعد حسب الدور
     */
    protected function buildAssistantKnowledge(string $role, ?Student $contextStudent): array
    {
        $base = [
            'مرحباً' => [
                'answer' => 'مرحباً بك! 👋 أنا المساعد الذكي لمنصة إيدو لينك. كيف يمكنني مساعدتك اليوم؟',
                'type' => 'greeting',
            ],
            'شكرا' => [
                'answer' => 'على الرحب والسعة! 😊 هل هناك شيء آخر يمكنني مساعدتك به؟',
                'type' => 'thank',
            ],
        ];

        if ($role === 'admin') {
            $base = array_merge($base, [
                'طالب' => [
                    'answer' => 'يمكنك إدارة الطلاب من قائمة "الطلاب" في لوحة التحكم. يمكنك إضافة طلاب جدد، تعيينهم للفصول، وتتبع أدائهم الأكاديمي. هل تريد معرفة المزيد عن ميزة معينة؟',
                    'type' => 'info',
                ],
                'معلم' => [
                    'answer' => 'يمكنك إدارة المعلمين من قائمة "المعلمين". يمكنك إضافة معلمين جدد، ربطهم بالمواد والفصول، وتتبع حمولتهم التدريسية.',
                    'type' => 'info',
                ],
                'تقرير' => [
                    'answer' => 'نظام التقارير يوفر لك إحصائيات شاملة عن الحضور والدرجات والطلاب. يمكنك تصفية التقارير حسب الصف والفصل والمادة والتاريخ.',
                    'type' => 'info',
                ],
                'جدول' => [
                    'answer' => 'من نظام الجدول الدراسي يمكنك إنشاء الجداول للفصول، نسخها بين الفصول، ومتابعة جداول المعلمين.',
                    'type' => 'info',
                ],
                'تحليل' => [
                    'answer' => 'لوحة التحليلات الذكية توفر لك رؤى متقدمة عن أداء الطلاب، اكتشاف الطلاب المعرضين للتعثر، وتوزيع المخاطر داخل المدرسة. تفضل بزيارة صفحة التحليلات الذكية.',
                    'type' => 'analytics',
                ],
            ]);
        } elseif ($role === 'teacher') {
            $base = array_merge($base, [
                'حضور' => [
                    'answer' => 'من نظام الحضور يمكنك تسجيل حضور وغياب الطلاب يومياً لفصولك، وعرض التقارير والإحصائيات المتعلقة بالحضور.',
                    'type' => 'info',
                ],
                'درجة' => [
                    'answer' => 'نظام الدرجات يتيح لك تسجيل درجات الأعمال الفصلية الشهرية (حضور 20 + واجبات 20 + مواظبة 20 + تحريري 40)، حساب المحصلة، وإدخال درجات النهائي (30). المجموع الكلي من 50.',
                    'type' => 'info',
                ],
                'واجب' => [
                    'answer' => 'من نظام الواجبات يمكنك إنشاء واجبات جديدة لفصولك، تحديد موعد التسليم، وتصحيح تسليمات الطلاب.',
                    'type' => 'info',
                ],
                'سلوك' => [
                    'answer' => 'نظام السلوك يتيح لك تسجيل ملاحظات السلوك الإيجابي والسلبي للطلاب مع نقاط تقييم.',
                    'type' => 'info',
                ],
            ]);
        } elseif ($role === 'parent') {
            $base = array_merge($base, [
                'ابن' => [
                    'answer' => 'من قائمة "أبنائي" يمكنك متابعة جميع أبنائك المسجلين، درجاتهم، حضورهم، سلوكهم، وجداولهم الدراسية.',
                    'type' => 'info',
                ],
                'درجة' => [
                    'answer' => 'يمكنك متابعة درجات أبنائك من صفحة الدرجات. سترى متوسط الدرجات لكل مادة وأحدث النتائج.',
                    'type' => 'info',
                ],
                'حضور' => [
                    'answer' => 'من صفحة الحضور يمكنك متابعة سجل حضور ابنك وغيابه مع الإحصائيات التفصيلية.',
                    'type' => 'info',
                ],
                'جدول' => [
                    'answer' => 'يمكنك عرض الجدول الدراسي لابنك من صفحة الجدول، مع معرفة المواد والحصص في كل يوم.',
                    'type' => 'info',
                ],
            ]);
        }

        // سياق الطالب (لولي الأمر)
        if ($contextStudent) {
            $insights = $this->studentInsights($contextStudent);
            $base['اداء'] = [
                'answer' => "تحليل أداء {$contextStudent->name}: {$insights['summary']} التقييم: {$insights['risk_assessment']['label']}.",
                'type' => 'insights',
                'data' => $insights,
            ];
            $base['تقيم'] = $base['اداء'];
            $base['تحليل'] = $base['اداء'];
        }

        return $base;
    }

    /**
     * مطابقة السؤال مع المعرفة
     */
    protected function matchQuestion(string $question, array $quiz): array
    {
        // فحص المطابقة المباشرة
        foreach ($quiz as $keyword => $data) {
            if (mb_strpos($question, mb_strtolower($keyword)) !== false) {
                return ['text' => $data['answer'], 'type' => $data['type'], 'data' => $data['data'] ?? null];
            }
        }

        // إجابة افتراضية
        return [
            'text' => 'شكراً لسؤالك! 😊 يمكنني مساعدتك في فهم ميزات المنصة مثل إدارة الطلاب والمعلمين والدرجات والحضور والتقارير. هل يمكنك توضيح سؤالك أكثر أو اختيار موضوع معين؟',
            'type' => 'fallback',
        ];
    }

    /**
     * اقتراحات الأسئلة حسب الدور
     */
    protected function getSuggestions(string $role): array
    {
        return match($role) {
            'admin' => ['كيف أضيف طالب؟', 'كيف أعمل تقرير؟', 'عرض التحليلات الذكية', 'كيف أدير الجدول؟'],
            'teacher' => ['كيف أسجل الحضور؟', 'كيف أضيف الدرجات؟', 'أريد إنشاء واجب', 'تسجيل سلوك'],
            'parent' => ['متابعة درجات ابني', 'متابعة الحضور', 'تحليل أداء ابني', 'عرض الجدول'],
            default => ['مرحباً', 'كيف أساعدك؟'],
        };
    }
}
