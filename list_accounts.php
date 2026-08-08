<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Teacher;
use App\Models\Guardian;
use App\Models\Student;

echo "List of accounts in the system:\n";
echo "======================================================\n";

foreach (User::with('role')->orderBy('id')->get() as $u) {
    $type = $u->is_super_admin ? 'SUPER-ADMIN' : ($u->role->slug ?? 'none');
    $linked = '';
    if ($u->isTeacher()) {
        $linked = $u->teacher ? ' | teacher_id=' . $u->teacher->id : ' | (no teacher record)';
    } elseif ($u->isParent()) {
        $linked = $u->guardian ? ' | guardian_id=' . $u->guardian->id : ' | (no guardian record)';
    }
    echo "- [{$type}] {$u->name} | phone={$u->phone} | email={$u->email}{$linked}\n";
}

echo "\nStudents:\n";
foreach (Student::all() as $s) {
    echo "- {$s->name} | student_id={$s->student_id} | guardian_id=" . ($s->guardian_id ?? 'none') . " | classroom_id=" . ($s->classroom_id ?? 'none') . "\n";
}

echo "\nFeature data summary (test data):\n";
echo "======================================================\n";
$tables = [
    'Fees' => App\Models\Fee::count(),
    'Student Fees' => App\Models\StudentFee::count(),
    'Payments' => App\Models\Payment::count(),
    'Expenses' => App\Models\Expense::count(),
    'Incomes' => App\Models\Income::count(),
    'Books' => App\Models\Book::count(),
    'Book Loans' => App\Models\BookLoan::count(),
    'Buses' => App\Models\Bus::count(),
    'Transport Routes' => App\Models\TransportRoute::count(),
    'Transport Students' => App\Models\TransportStudent::count(),
    'Question Bank' => App\Models\QuestionBank::count(),
    'Online Quizzes' => App\Models\OnlineQuiz::count(),
    'Quiz Questions' => App\Models\QuizQuestion::count(),
    'Quiz Attempts' => App\Models\QuizAttempt::count(),
    'Classroom Materials' => App\Models\ClassroomMaterial::count(),
];
$maxLen = max(array_map('strlen', array_keys($tables)));
foreach ($tables as $name => $count) {
    echo "- " . str_pad($name, $maxLen, ' ', STR_PAD_RIGHT) . " : {$count}\n";
}

