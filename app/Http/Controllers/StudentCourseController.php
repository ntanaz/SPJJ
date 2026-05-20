<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('modules.activities')->paginate(10);
        $user = auth()->user();
        if ($user && $user->hasRole('siswa')) {
            foreach ($courses as $course) {
                $activityIds = \DB::table('learning_activities')
                    ->whereIn('module_id', $course->modules->pluck('id'))
                    ->pluck('id');
                
                $totalActivities = $activityIds->count();
                $completedCount = $totalActivities > 0 ? \DB::table('learning_activity_progress')
                    ->where('user_id', $user->id)
                    ->whereIn('learning_activity_id', $activityIds)
                    ->where('is_completed', true)
                    ->count() : 0;

                $course->progress_percent = $totalActivities > 0 ? round(($completedCount / $totalActivities) * 100) : 0;
                $course->completed_count = $completedCount;
            }
        }
        return view('student.courses', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load([
            'materials',
            'modules' => function($query) {
                $query->orderBy('order_number');
            },
            'modules.activities' => function($query) {
                $query->orderBy('order_number');
            },
            'modules.activities.progress',
            'modules.activities.material',
            'modules.activities.quiz',
            'modules.activities.assignment',
            'modules.activities.discussion',
            'modules.activities.video',
            'attendances' => function($query) {
                $query->latest();
            }
        ]);

        $completedCount = 0;
        $progressPercent = 0;

        if (auth()->user()->hasRole('siswa')) {
            $activityIds = \DB::table('learning_activities')
                ->whereIn('module_id', $course->modules->pluck('id'))
                ->pluck('id');
            
            $totalActivities = $activityIds->count();
            $completedCount = $totalActivities > 0 ? \DB::table('learning_activity_progress')
                ->where('user_id', auth()->id())
                ->whereIn('learning_activity_id', $activityIds)
                ->where('is_completed', true)
                ->count() : 0;
            
            $progressPercent = $totalActivities > 0 ? round(($completedCount / $totalActivities) * 100) : 0;
        }

        return view('student.course_details', compact('course', 'completedCount', 'progressPercent'));
    }
}
