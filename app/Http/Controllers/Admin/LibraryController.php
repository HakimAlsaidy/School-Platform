<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Book;
use App\Models\BookLoan;
use App\Models\Student;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        $books = Book::withCount('loans')->latest()->get();
        $loans = BookLoan::with(['book', 'student', 'issuedBy'])
            ->where('status', '!=', 'returned')
            ->latest()
            ->get();
        $students = Student::where('is_active', true)->get();

        return view('admin.library.index', compact('books', 'loans', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string'],
            'publisher' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'total_copies' => ['required', 'integer', 'min:1'],
            'shelf_location' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $book = Book::create([
            'school_id' => auth()->user()->school_id,
            'title' => $validated['title'],
            'author' => $validated['author'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'publisher' => $validated['publisher'] ?? null,
            'category' => $validated['category'] ?? null,
            'total_copies' => $validated['total_copies'],
            'available_copies' => $validated['total_copies'],
            'shelf_location' => $validated['shelf_location'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        ActivityLog::log('add_book', "إضافة كتاب: {$book->title}", $book);
        return back()->with('success', 'تمت إضافة الكتاب بنجاح.');
    }

    public function loan(Request $request)
    {
        $validated = $request->validate([
            'book_id' => ['required', 'exists:books,id'],
            'student_id' => ['required', 'exists:students,id'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ]);

        $book = Book::findOrFail($validated['book_id']);
        if ($book->available_copies <= 0) {
            return back()->with('error', 'لا توجد نسخ متاحة من هذا الكتاب.');
        }

        $loan = BookLoan::create([
            'school_id' => auth()->user()->school_id,
            'book_id' => $book->id,
            'student_id' => $validated['student_id'],
            'loan_date' => now()->toDateString(),
            'due_date' => $validated['due_date'],
            'status' => 'borrowed',
            'notes' => $validated['notes'] ?? null,
            'issued_by' => auth()->id(),
        ]);

        $book->decrement('available_copies');

        ActivityLog::log('loan_book', "إعارة كتاب: {$book->title}", $loan);
        return back()->with('success', 'تمت عملية الإعارة بنجاح.');
    }

    public function returnLoan(BookLoan $loan)
    {
        if ($loan->status === 'returned') {
            return back()->with('info', 'تمت إعادة هذا الكتاب مسبقاً.');
        }

        $loan->update([
            'status' => 'returned',
            'return_date' => now()->toDateString(),
        ]);

        $loan->book()->increment('available_copies');

        ActivityLog::log('return_book', "إعادة كتاب: {$loan->book->title}", $loan);
        return back()->with('success', 'تمت إعادة الكتاب بنجاح.');
    }

    public function destroy(Book $book)
    {
        $title = $book->title;
        $book->delete();
        ActivityLog::log('delete_book', "حذف كتاب: {$title}");
        return back()->with('success', 'تم حذف الكتاب بنجاح.');
    }
}
