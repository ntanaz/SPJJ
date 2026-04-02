<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $upcomingAssignments = collect();
    if (auth()->check() && auth()->user()->hasRole('siswa')) {
        $upcomingAssignments = \App\Models\Assignment::with('course')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->endOfWeek())
            ->orderBy('deadline', 'asc')
            ->get();
    }
    return view('dashboard', compact('upcomingAssignments'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::resource('courses', \App\Http\Controllers\CourseController::class);
    });

    // Guru Routes
    Route::middleware(['role:guru|admin'])->group(function () {
        Route::resource('materials', \App\Http\Controllers\MaterialController::class);
        Route::resource('assignments', \App\Http\Controllers\AssignmentController::class);
        Route::resource('quizzes', \App\Http\Controllers\QuizController::class)->except(['edit', 'update']);
        Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::delete('quiz-questions/{question}', [\App\Http\Controllers\QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
    });

    // Siswa Routes
    Route::middleware(['role:siswa'])->group(function () {
        Route::get('my-courses', [\App\Http\Controllers\StudentCourseController::class, 'index'])->name('student.courses');
        Route::post('assignments/{assignment}/submit', [\App\Http\Controllers\StudentAssignmentController::class, 'submit'])->name('student.assignments.submit');
        
        // Siswa Quiz Routes
        Route::get('quizzes/{quiz}/start', [\App\Http\Controllers\StudentQuizController::class, 'show'])->name('student.quizzes.show');
        Route::post('quizzes/{quiz}/start', [\App\Http\Controllers\StudentQuizController::class, 'start'])->name('student.quizzes.start');
        Route::get('quiz-attempts/{attempt}', [\App\Http\Controllers\StudentQuizController::class, 'attempt'])->name('student.quizzes.attempt');
        Route::post('quiz-attempts/{attempt}/submit', [\App\Http\Controllers\StudentQuizController::class, 'submit'])->name('student.quizzes.submit');
        Route::get('quiz-attempts/{attempt}/result', [\App\Http\Controllers\StudentQuizController::class, 'result'])->name('student.quizzes.result');
        
        // Siswa Attendance Routes
        Route::get('attendances/{attendance}', [\App\Http\Controllers\StudentAttendanceController::class, 'show'])->name('student.attendances.show');
        Route::post('attendances/{attendance}/submit', [\App\Http\Controllers\StudentAttendanceController::class, 'submit'])->name('student.attendances.submit');
    });

    // Shared Course View Routes
    Route::middleware(['role:siswa|guru|admin'])->group(function () {
        Route::get('my-courses/{course}', [\App\Http\Controllers\StudentCourseController::class, 'show'])->name('student.courses.show');
        Route::get('assignments/{assignment}', [\App\Http\Controllers\StudentAssignmentController::class, 'show'])->name('student.assignments.show');
    });

    // Discussion Topic Routes (Guru/Admin only)
    Route::middleware(['role:guru|admin'])->group(function () {
        Route::post('courses/{course}/discussions', [\App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
        Route::delete('discussions/{discussion}', [\App\Http\Controllers\DiscussionController::class, 'destroy'])->name('discussions.destroy');
        Route::post('discussion-replies/{reply}/grade', [\App\Http\Controllers\DiscussionReplyController::class, 'grade'])->name('discussion_replies.grade');
        
        // Attendance Routes (Guru/Admin)
        Route::get('attendances/create', [\App\Http\Controllers\AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('attendances/guru/{attendance}', [\App\Http\Controllers\AttendanceController::class, 'show'])->name('attendances.show');
        Route::delete('attendances/{attendance}', [\App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendances.destroy');
    });

    // Discussion Reply Routes (Shared Siswa/Guru/Admin)
    Route::get('discussions/{discussion}', [\App\Http\Controllers\DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/replies', [\App\Http\Controllers\DiscussionReplyController::class, 'store'])->name('discussion_replies.store');
    Route::delete('discussion-replies/{reply}', [\App\Http\Controllers\DiscussionReplyController::class, 'destroy'])->name('discussion_replies.destroy');
});

require __DIR__.'/auth.php';
