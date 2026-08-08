<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\School;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Fee;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\Bus;
use App\Models\TransportRoute;
use App\Models\TransportStudent;
use App\Models\QuestionBank;
use App\Models\OnlineQuiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\ClassroomMaterial;
use Illuminate\Support\Str;

echo "🚀 بدء إضافة البيانات التجريبية للوحدات الوظيفية...\n\n";

$school = School::where('subdomain', 'demo')->first();
if (!$school) {
    echo "❌ المدرسة التجريبية غير موجودة. شغّل seed_accounts.php أولاً.\n";
    exit(1);
}

$admin = User::where('phone', '0500000002')->first();
$teacher1 = Teacher::where('school_id', $school->id)->first();
if (!$teacher1) {
    echo "❌ لا يوجد معلم. شغّل seed_accounts.php أولاً.\n";
    exit(1);
}
$students = Student::where('school_id', $school->id)->get();
$classrooms = \App\Models\Classroom::where('school_id', $school->id)->get();
$grades = \App\Models\Grade::where('school_id', $school->id)->get();
$subjects = \App\Models\Subject::where('school_id', $school->id)->get();

$sid = $school->id;
$recBy = $admin ? $admin->id : 1;

// ==========================================
// 💰 1. نظام الفواتير والرسوم الدراسية
// ==========================================
echo "💰 إضافة بيانات الرسوم والمالية...\n";

$feesData = [
    ['title' => 'الرسوم الدراسية السنوية', 'amount' => 15000, 'type' => 'tuition', 'frequency' => 'yearly', 'status' => 'active'],
    ['title' => 'رسوم الكتب الدراسية', 'amount' => 2500, 'type' => 'books', 'frequency' => 'term', 'status' => 'active'],
    ['title' => 'رسوم النقل المدرسي', 'amount' => 3000, 'type' => 'transport', 'frequency' => 'term', 'status' => 'active'],
    ['title' => 'رسوم الأنشطة الطلابية', 'amount' => 1000, 'type' => 'activities', 'frequency' => 'one_time', 'status' => 'active'],
    ['title' => 'رسوم المعسكر الصيفي', 'amount' => 5000, 'type' => 'other', 'frequency' => 'one_time', 'status' => 'draft'],
];

foreach ($feesData as $i => $data) {
    $fee = Fee::firstOrCreate(
        ['school_id' => $sid, 'title' => $data['title']],
        [
            'description' => "رسوم {$data['title']} للعام الدراسي الحالي",
            'amount' => $data['amount'],
            'type' => $data['type'],
            'frequency' => $data['frequency'],
            'status' => $data['status'],
            'due_date' => now()->addMonths($i + 1)->toDateString(),
            'is_installment' => $i === 0 ? true : false,
            'installments_count' => $i === 0 ? 4 : null,
        ]
    );
    echo "  ✅ رسوم: {$fee->title} - {$fee->amount} ريال\n";

    // فرض الرسوم على الطلاب
    foreach ($students as $stu) {
        $status = $i === 0 ? 'paid' : ($i <= 2 ? 'pending' : 'partial');
        $paid = $status === 'paid' ? $data['amount'] : ($status === 'partial' ? $data['amount'] * 0.5 : 0);
        StudentFee::firstOrCreate(
            ['fee_id' => $fee->id, 'student_id' => $stu->id],
            [
                'school_id' => $sid,
                'status' => $status,
                'amount_due' => $data['amount'],
                'amount_paid' => $paid,
                'due_date' => now()->addMonths($i + 1)->toDateString(),
            ]
        );
    }
}

echo "  ✅ تم فرض الرسوم على " . count($students) . " طالب\n";

// المدفوعات
$paymentMethods = ['cash', 'card', 'transfer', 'online', 'wallet'];
$paymentCount = 0;
foreach ($students->take(2) as $stu) {
    foreach ($stu->studentFees()->where('amount_paid', '>', 0)->get() as $sf) {
        $payRef = 'PAY-' . strtoupper(Str::random(8));
        Payment::firstOrCreate(
            ['payment_ref' => $payRef],
            [
                'school_id' => $sid,
                'student_fee_id' => $sf->id,
                'student_id' => $stu->id,
                'user_id' => $recBy,
                'amount' => $sf->amount_paid,
                'method' => $paymentMethods[array_rand($paymentMethods)],
                'status' => 'completed',
                'payment_date' => now()->subDays(rand(1, 30))->toDateString(),
                'notes' => 'دفعة إلكترونية للرسوم الدراسية',
            ]
        );
        $paymentCount++;
    }
}
echo "  ✅ تم إضافة {$paymentCount} دفعة مالية\n";

// المصاريف
$expenses = [
    ['title' => 'رواتب المعلمين - شهر', 'amount' => 45000, 'category' => 'salaries'],
    ['title' => 'فاتورة الكهرباء', 'amount' => 3500, 'category' => 'utilities'],
    ['title' => 'لوازم مكتبية', 'amount' => 1800, 'category' => 'supplies'],
    ['title' => 'صيانة المبنى', 'amount' => 6500, 'category' => 'maintenance'],
    ['title' => 'وقود الحافلات', 'amount' => 2800, 'category' => 'transport'],
];
foreach ($expenses as $i => $data) {
    Expense::create([
        'school_id' => $sid,
        'title' => $data['title'],
        'description' => "مصروف {$data['title']}",
        'amount' => $data['amount'],
        'category' => $data['category'],
        'expense_date' => now()->subDays(rand(1, 60))->toDateString(),
        'recorded_by' => $recBy,
    ]);
}
echo "  ✅ تم إضافة " . count($expenses) . " مصروف\n";

// الإيرادات
$incomes = [
    ['title' => 'إيراد الرسوم الدراسية', 'amount' => 120000, 'category' => 'tuition'],
    ['title' => 'تبرع من مجلس الآباء', 'amount' => 15000, 'category' => 'donation'],
    ['title' => 'إيجار القاعة الرياضية', 'amount' => 8000, 'category' => 'rental'],
];
foreach ($incomes as $i => $data) {
    Income::create([
        'school_id' => $sid,
        'title' => $data['title'],
        'description' => "إيراد {$data['title']}",
        'amount' => $data['amount'],
        'category' => $data['category'],
        'income_date' => now()->subDays(rand(1, 60))->toDateString(),
        'recorded_by' => $recBy,
    ]);
}
echo "  ✅ تم إضافة " . count($incomes) . " إيراد\n";

// ==========================================
// 📚 2. نظام المكتبة
// ==========================================
echo "📚 إضافة بيانات المكتبة...\n";

$books = [
    ['title' => 'القراءة العربية للصف الأول', 'author' => 'د. محمد الحسن', 'isbn' => '978-3-16-148410-0', 'category' => 'تعليمي', 'total' => 20],
    ['title' => 'مبادئ الرياضيات', 'author' => 'أ. خالد العلي', 'isbn' => '978-1-4028-9462-6', 'category' => 'تعليمي', 'total' => 25],
    ['title' => 'موسوعة العلوم للأطفال', 'author' => 'د. سارة أحمد', 'isbn' => '978-0-596-52068-7', 'category' => 'علمي', 'total' => 15],
    ['title' => 'مغامرات في الفضاء', 'author' => 'جون كينيدي', 'isbn' => '978-0-13-468599-1', 'category' => 'قصص', 'total' => 10],
    ['title' => 'أساسيات اللغة الإنجليزية', 'author' => 'د. عائشة بنت سعود', 'isbn' => '978-0-321-87758-1', 'category' => 'تعليمي', 'total' => 18],
];

foreach ($books as $data) {
    Book::firstOrCreate(
        ['school_id' => $sid, 'title' => $data['title']],
        [
            'author' => $data['author'],
            'isbn' => $data['isbn'],
            'publisher' => 'دار النشر العربية',
            'category' => $data['category'],
            'total_copies' => $data['total'],
            'available_copies' => max(1, $data['total'] - 2),
            'shelf_location' => 'رف ' . rand(1, 10) . '-' . rand(1, 5),
            'description' => "كتاب {$data['title']} - نسخة تجريبية",
        ]
    );
}
echo "  ✅ تم إضافة " . count($books) . " كتاب\n";

// إعارات الكتب
$booksAll = Book::where('school_id', $sid)->get();
$loanCount = 0;
foreach ($students->take(2) as $stu) {
    $book = $booksAll->random();
    $b = Book::where('school_id', $sid)->where('available_copies', '>', 0)->first();
    if ($b) {
        BookLoan::firstOrCreate(
            ['school_id' => $sid, 'book_id' => $b->id, 'student_id' => $stu->id, 'loan_date' => now()->subDays(rand(5, 20))->toDateString()],
            [
                'due_date' => now()->addDays(rand(5, 15))->toDateString(),
                'return_date' => null,
                'status' => 'borrowed',
                'notes' => 'إعارة تجريبية',
                'issued_by' => $recBy,
            ]
        );
        $b->decrement('available_copies');
        $loanCount++;
    }
}
echo "  ✅ تم إضافة {$loanCount} إعارة كتب\n";

// ==========================================
// 🚌 3. نظام النقل المدرسي
// ==========================================
echo "🚌 إضافة بيانات النقل المدرسي...\n";

$buses = [
    ['bus_number' => 'BUS-001', 'plate_number' => 'أ ب ج 1234', 'driver_name' => 'عم محمد', 'driver_phone' => '0551111111', 'capacity' => 40],
    ['bus_number' => 'BUS-002', 'plate_number' => 'د هـ و 5678', 'driver_name' => 'عم حسن', 'driver_phone' => '0552222222', 'capacity' => 45],
    ['bus_number' => 'BUS-003', 'plate_number' => 'ز ح ط 9012', 'driver_name' => 'عم علي', 'driver_phone' => '0553333333', 'capacity' => 35],
];
foreach ($buses as $data) {
    Bus::firstOrCreate(
        ['school_id' => $sid, 'bus_number' => $data['bus_number']],
        [
            'plate_number' => $data['plate_number'],
            'capacity' => $data['capacity'],
            'driver_name' => $data['driver_name'],
            'driver_phone' => $data['driver_phone'],
            'supervisor_name' => 'مشرف الحافلة',
            'supervisor_phone' => '0554444444',
            'is_active' => true,
        ]
    );
}
echo "  ✅ تم إضافة " . count($buses) . " حافلة\n";

$routesData = [
    ['name' => 'خط حي الملقا', 'pickup_time' => '06:30:00', 'dropoff_time' => '13:30:00'],
    ['name' => 'خط حي النرجس', 'pickup_time' => '06:45:00', 'dropoff_time' => '13:45:00'],
    ['name' => 'خط حي الياسمين', 'pickup_time' => '07:00:00', 'dropoff_time' => '14:00:00'],
];
$routeCount = 0;
foreach ($routesData as $i => $data) {
    $bus = Bus::where('school_id', $sid)->skip($i)->first();
    if ($bus) {
        TransportRoute::firstOrCreate(
            ['school_id' => $sid, 'name' => $data['name']],
            [
                'bus_id' => $bus->id,
                'description' => "مسار {$data['name']}",
                'pickup_time' => $data['pickup_time'],
                'dropoff_time' => $data['dropoff_time'],
                'is_active' => true,
            ]
        );
        $routeCount++;
    }
}
echo "  ✅ تم إضافة {$routeCount} مسار نقل\n";

// ربط الطلاب بالمسارات
$routes = TransportRoute::where('school_id', $sid)->get();
$assignCount = 0;
foreach ($students as $i => $stu) {
    $route = $routes->get($i % count($routes));
    if ($route) {
        TransportStudent::firstOrCreate(
            ['route_id' => $route->id, 'student_id' => $stu->id],
            [
                'school_id' => $sid,
                'pickup_point' => 'محطة ' . ($i + 1),
                'dropoff_point' => 'المدرسة',
                'is_active' => true,
            ]
        );
        $assignCount++;
    }
}
echo "  ✅ تم ربط {$assignCount} طالب بالمسارات\n";

// ==========================================
// 📝 4. نظام الاختبارات الإلكترونية
// ==========================================
echo "📝 إضافة بيانات بنك الأسئلة والاختبارات...\n";

$questionTemplates = [
    ['type' => 'multiple_choice', 'question' => 'ما هي عاصمة المملكة العربية السعودية؟', 'options' => ['الرياض', 'جدة', 'مكة', 'الدمام'], 'correct_answer' => 'الرياض', 'difficulty' => 'easy'],
    ['type' => 'multiple_choice', 'question' => 'كم يساوي 5 + 7؟', 'options' => ['10', '11', '12', '13'], 'correct_answer' => '12', 'difficulty' => 'easy'],
    ['type' => 'true_false', 'question' => 'الماء يغلي عند درجة حرارة 100 مئوية', 'options' => null, 'correct_answer' => 'true', 'difficulty' => 'medium'],
    ['type' => 'multiple_choice', 'question' => 'ما هو أكبر كوكب في المجموعة الشمسية؟', 'options' => ['المريخ', 'المشتري', 'زحل', 'الأرض'], 'correct_answer' => 'المشتري', 'difficulty' => 'medium'],
    ['type' => 'short_answer', 'question' => 'اذكر عاصمة مصر', 'options' => null, 'correct_answer' => 'القاهرة', 'difficulty' => 'easy'],
    ['type' => 'essay', 'question' => 'اكتب فقرة قصيرة عن أهمية القراءة', 'options' => null, 'correct_answer' => 'القراءة تفتح آفاق المعرفة', 'difficulty' => 'hard'],
];

$qCount = 0;
foreach ($questionTemplates as $i => $data) {
    $subject = $subjects->get($i % count($subjects));
    $grade = $grades->first();
    QuestionBank::firstOrCreate(
        ['school_id' => $sid, 'subject_id' => $subject->id, 'question' => $data['question']],
        [
            'grade_id' => $grade ? $grade->id : null,
            'teacher_id' => $teacher1->id,
            'type' => $data['type'],
            'options' => $data['options'],
            'correct_answer' => $data['correct_answer'],
            'points' => $data['difficulty'] === 'hard' ? 5 : 2,
            'difficulty' => $data['difficulty'],
        ]
    );
    $qCount++;
}
echo "  ✅ تم إضافة {$qCount} سؤال في بنك الأسئلة\n";

// إنشاء اختبارات
$quizzesData = [
    ['title' => 'اختبار الرياضيات - الفصل الأول', 'duration' => 30, 'total_points' => 12],
    ['title' => 'اختبار العلوم العامة', 'duration' => 45, 'total_points' => 7],
    ['title' => 'اختبار اللغة العربية', 'duration' => 20, 'total_points' => 2],
];

$quizCount = 0;
foreach ($quizzesData as $i => $data) {
    $subject = $subjects->get($i % count($subjects));
    $classroom = $classrooms->first();
    $quiz = OnlineQuiz::firstOrCreate(
        ['school_id' => $sid, 'title' => $data['title']],
        [
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'classroom_id' => $classroom ? $classroom->id : null,
            'description' => $data['title'],
            'duration_minutes' => $data['duration'],
            'total_points' => $data['total_points'],
            'is_published' => $i < 2,
            'start_at' => now()->subDays(rand(1, 10)),
            'end_at' => now()->addDays(rand(5, 20)),
            'allow_retake' => false,
        ]
    );

    // إضافة أسئلة للاختبار من بنك الأسئلة
    $bankQuestions = QuestionBank::where('school_id', $sid)->where('subject_id', $subject->id)->get();
    if ($bankQuestions->isEmpty()) {
        $bankQuestions = QuestionBank::where('school_id', $sid)->get();
    }
    $order = 0;
    foreach ($bankQuestions->take(3) as $bq) {
        QuizQuestion::firstOrCreate(
            ['online_quiz_id' => $quiz->id, 'question' => $bq->question],
            [
                'question_bank_id' => $bq->id,
                'type' => $bq->type,
                'options' => $bq->options,
                'correct_answer' => $bq->correct_answer,
                'points' => $bq->points,
                'order' => $order++,
            ]
        );
    }
    $quizCount++;
}
echo "  ✅ تم إضافة {$quizCount} اختبار إلكتروني\n";

// محاولات الاختبار
$attemptCount = 0;
foreach ($students->take(2) as $stu) {
    $quiz = OnlineQuiz::where('school_id', $sid)->where('is_published', true)->first();
    if ($quiz) {
        $questions = $quiz->questions;
        $maxScore = $questions->sum('points');
        $score = rand(round($maxScore * 0.6), $maxScore);
        QuizAttempt::firstOrCreate(
            ['online_quiz_id' => $quiz->id, 'student_id' => $stu->id],
            [
                'score' => $score,
                'max_score' => $maxScore,
                'answers' => ['q1' => 'answer1', 'q2' => 'answer2'],
                'started_at' => now()->subHours(rand(1, 48)),
                'submitted_at' => now()->subHours(rand(1, 48)),
                'status' => 'submitted',
            ]
        );
        $attemptCount++;
    }
}
echo "  ✅ تم إضافة {$attemptCount} محاولة اختبار\n";

// ==========================================
// 📁 5. المواد الدراسية
// ==========================================
echo "📁 إضافة المواد الدراسية...\n";

$materials = [
    ['title' => 'ملخص قواعد اللغة العربية', 'type' => 'text', 'content' => 'ملخص شامل لقواعد النحو والصرف للفصل الدراسي الأول.'],
    ['title' => 'روابط فيديوهات الرياضيات', 'type' => 'link', 'external_url' => 'https://www.youtube.com/playlist?list=math-lessons'],
['title' => 'تطبيق عملي العلوم', 'type' => 'file', 'file_path' => 'uploads/materials/science-lab.pdf', 'external_url' => null],
    ['title' => 'محاضرة مسجلة - الفيزياء', 'type' => 'video', 'external_url' => 'https://www.youtube.com/watch?v=physics-lecture'],
];

$materialCount = 0;
foreach ($materials as $i => $data) {
    $classroom = $classrooms->get($i % count($classrooms)) ?: $classrooms->first();
    $subject = $subjects->get($i % count($subjects));
    ClassroomMaterial::firstOrCreate(
        ['school_id' => $sid, 'title' => $data['title']],
        [
            'classroom_id' => $classroom ? $classroom->id : null,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'description' => "مادة دراسية: {$data['title']}",
            'type' => $data['type'],
            'file_path' => $data['file_path'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'content' => $data['content'] ?? null,
        ]
    );
    $materialCount++;
}
echo "  ✅ تم إضافة {$materialCount} مادة دراسية\n";

echo "\n========================================\n";
echo "🎉 تم إضافة جميع البيانات التجريبية بنجاح!\n";
echo "========================================\n";
