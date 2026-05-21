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
        Route::resource('quizzes', \App\Http\Controllers\QuizController::class);
        Route::post('quizzes/{quiz}/questions', [\App\Http\Controllers\QuizController::class, 'storeQuestion'])->name('quizzes.questions.store');
        Route::put('quiz-questions/{question}', [\App\Http\Controllers\QuizController::class, 'updateQuestion'])->name('quizzes.questions.update');
        Route::delete('quiz-questions/{question}', [\App\Http\Controllers\QuizController::class, 'destroyQuestion'])->name('quizzes.questions.destroy');
        
        // Learning Activities Teacher Management
        Route::post('modules/{module}/activities', [\App\Http\Controllers\LearningActivityController::class, 'store'])->name('modules.activities.store');
        Route::put('activities/{activity}', [\App\Http\Controllers\LearningActivityController::class, 'update'])->name('activities.update');
        Route::delete('activities/{activity}', [\App\Http\Controllers\LearningActivityController::class, 'destroy'])->name('activities.destroy');
        Route::post('modules/{module}/activities/reorder', [\App\Http\Controllers\LearningActivityController::class, 'reorder'])->name('modules.activities.reorder');

        // Video Learning Teacher Management
        Route::post('modules/{module}/videos', [\App\Http\Controllers\VideoController::class, 'store'])->name('modules.videos.store');
        Route::get('videos/{video}/manage', [\App\Http\Controllers\VideoController::class, 'manage'])->name('videos.manage');
        Route::post('videos/{video}/upload-file', [\App\Http\Controllers\VideoController::class, 'uploadFile'])->name('videos.upload_file');
        Route::post('videos/{video}/quizzes', [\App\Http\Controllers\VideoController::class, 'storeQuiz'])->name('videos.quizzes.store');
        Route::put('video-quizzes/{quiz}', [\App\Http\Controllers\VideoController::class, 'updateQuiz'])->name('videos.quizzes.update');
        Route::delete('video-quizzes/{quiz}', [\App\Http\Controllers\VideoController::class, 'destroyQuiz'])->name('videos.quizzes.destroy');
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
        
        // Learning Activities Student Routes
        Route::get('activities/{activity}', [\App\Http\Controllers\LearningActivityController::class, 'show'])->name('student.activities.show');
        Route::post('activities/{activity}/complete', [\App\Http\Controllers\LearningActivityController::class, 'complete'])->name('student.activities.complete');
        Route::get('discussions', [\App\Http\Controllers\DiscussionController::class, 'index'])->name('discussions.index');
        
        // Video Learning Student Routes
        Route::post('videos/{video}/log', [\App\Http\Controllers\VideoController::class, 'logProgress'])->name('student.videos.log');
        Route::post('videos/{video}/quiz-submit', [\App\Http\Controllers\VideoController::class, 'submitQuizAnswer'])->name('student.videos.quiz_submit');
        Route::get('videos/{video}/stream', [\App\Http\Controllers\VideoController::class, 'stream'])->name('videos.stream');
        Route::get('materials/{material}/stream', [\App\Http\Controllers\StudentMaterialController::class, 'streamVideo'])->name('materials.stream_video');
        
        // Discovery Learning Steps Routes
        Route::post('materials/{material}/step', [\App\Http\Controllers\StudentMaterialController::class, 'completeStep'])->name('student.materials.complete_step');
        Route::post('materials/{material}/video-quiz', [\App\Http\Controllers\StudentMaterialController::class, 'submitVideoQuizAnswer'])->name('student.materials.submit_video_quiz');
        Route::post('materials/{material}/coding-quiz', [\App\Http\Controllers\StudentMaterialController::class, 'submitCodingQuiz'])->name('student.materials.submit_coding_quiz');
        Route::post('materials/{material}/reflection', [\App\Http\Controllers\StudentMaterialController::class, 'submitReflection'])->name('student.materials.submit_reflection');

        // Teacher Management of Discovery Learning Steps
        Route::post('materials/{material}/coding-quiz/save', [\App\Http\Controllers\StudentMaterialController::class, 'saveCodingQuiz'])->name('materials.save_coding_quiz');
        Route::post('materials/{material}/video-questions', [\App\Http\Controllers\StudentMaterialController::class, 'saveInteractiveVideoQuestions'])->name('materials.save_video_questions');
        Route::post('materials/{material}/mind-map', [\App\Http\Controllers\StudentMaterialController::class, 'uploadMindMap'])->name('materials.upload_mind_map');
        Route::post('materials/{material}/upload-video', [\App\Http\Controllers\StudentMaterialController::class, 'uploadVideo'])->name('materials.upload_video');
        Route::post('coding-attempts/{attempt}/grade', [\App\Http\Controllers\StudentMaterialController::class, 'gradeAttempt'])->name('materials.grade_attempt');
    });

    // Teacher & Admin Routes
    Route::middleware(['role:guru|teacher|admin'])->group(function () {
        Route::get('analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        
        Route::post('discussions/{discussion}/pin', [\App\Http\Controllers\DiscussionController::class, 'pin'])->name('discussions.pin');
        Route::post('discussions/{discussion}/lock', [\App\Http\Controllers\DiscussionController::class, 'lock'])->name('discussions.lock');
        Route::post('discussion-replies/{reply}/grade', [\App\Http\Controllers\DiscussionReplyController::class, 'grade'])->name('discussion_replies.grade');
        
        // Attendance Routes (Guru/Admin)
        Route::get('attendances/create', [\App\Http\Controllers\AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [\App\Http\Controllers\AttendanceController::class, 'store'])->name('attendances.store');
        Route::get('attendances/guru/{attendance}', [\App\Http\Controllers\AttendanceController::class, 'show'])->name('attendances.show');
        Route::delete('attendances/{attendance}', [\App\Http\Controllers\AttendanceController::class, 'destroy'])->name('attendances.destroy');

        // Module/Chapter Routes (Guru/Admin)
        Route::post('courses/{course}/modules', [\App\Http\Controllers\ModuleController::class, 'store'])->name('courses.modules.store');
        Route::put('modules/{module}', [\App\Http\Controllers\ModuleController::class, 'update'])->name('modules.update');
        Route::delete('modules/{module}', [\App\Http\Controllers\ModuleController::class, 'destroy'])->name('modules.destroy');
    });

    // Discussion Routes (Shared Siswa/Guru/Admin)
    Route::post('courses/{course}/discussions', [\App\Http\Controllers\DiscussionController::class, 'store'])->name('discussions.store');
    Route::post('materials/{material}/discussions', [\App\Http\Controllers\DiscussionController::class, 'storeForMaterial'])->name('discussions.store_material');
    Route::delete('discussions/{discussion}', [\App\Http\Controllers\DiscussionController::class, 'destroy'])->name('discussions.destroy');
    Route::get('discussions/{discussion}', [\App\Http\Controllers\DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('discussions/{discussion}/replies', [\App\Http\Controllers\DiscussionReplyController::class, 'store'])->name('discussion_replies.store');
    Route::delete('discussion-replies/{reply}', [\App\Http\Controllers\DiscussionReplyController::class, 'destroy'])->name('discussion_replies.destroy');
});

require __DIR__.'/auth.php';
