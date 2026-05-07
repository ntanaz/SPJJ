<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\StudentDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class);
        Route::resource('learning-resources', \App\Http\Controllers\Admin\LearningResourceController::class)->except(['edit', 'update', 'show']);
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::put('roles/{role}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
    });
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });

    // Teacher/Guru Routes
    Route::middleware(['role:guru|teacher|admin'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\TeacherDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('profile', [\App\Http\Controllers\TeacherProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [\App\Http\Controllers\TeacherProfileController::class, 'update'])->name('profile.update');
        
        Route::get('grade-recap', [\App\Http\Controllers\TeacherGradeRecapController::class, 'index'])->name('grade-recap.index');
        Route::get('grade-recap/export', [\App\Http\Controllers\TeacherGradeRecapController::class, 'export'])->name('grade-recap.export');
        
        Route::get('notifications', [\App\Http\Controllers\TeacherNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [\App\Http\Controllers\TeacherNotificationController::class, 'markAsRead'])->name('notifications.read');
        
        Route::get('assignments/{assignment}/submissions', [\App\Http\Controllers\AssignmentController::class, 'submissions'])->name('assignments.submissions');
        Route::post('submissions/{submission}/grade', [\App\Http\Controllers\AssignmentController::class, 'gradeSubmission'])->name('submissions.grade');
    });

    Route::middleware(['role:guru|teacher|admin'])->group(function () {
        Route::resource('courses', \App\Http\Controllers\CourseController::class);
        Route::resource('materials', \App\Http\Controllers\MaterialController::class);
        Route::resource('assignments', \App\Http\Controllers\AssignmentController::class);
        Route::resource('quizzes', \App\Http\Controllers\QuizController::class)->except(['edit', 'update']);
        Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::delete('quiz-questions/{question}', [\App\Http\Controllers\QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
    });

    // Siswa Routes
    Route::middleware(['role:siswa'])->group(function () {
        Route::get('my-courses', [\App\Http\Controllers\StudentCourseController::class, 'index'])->name('student.courses');
        Route::get('todos', [\App\Http\Controllers\StudentTodoController::class, 'index'])->name('student.todos');
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
        Route::get('materials/{material}', [\App\Http\Controllers\StudentMaterialController::class, 'show'])->name('student.materials.show');
        Route::post('materials/{material}/complete', [\App\Http\Controllers\StudentMaterialController::class, 'complete'])->name('student.materials.complete');
    });

    // Shared Teacher & Admin Routes
    Route::middleware(['role:guru|teacher|admin'])->group(function () {
        Route::get('analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        
        Route::post('courses/{course}/discussions', [\App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
        Route::delete('discussions/{discussion}', [\App\Http\Controllers\DiscussionController::class, 'destroy'])->name('discussions.destroy');
        Route::post('discussions/{discussion}/pin', [\App\Http\Controllers\DiscussionController::class, 'pin'])->name('discussions.pin');
        Route::post('discussions/{discussion}/lock', [\App\Http\Controllers\DiscussionController::class, 'lock'])->name('discussions.lock');
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
