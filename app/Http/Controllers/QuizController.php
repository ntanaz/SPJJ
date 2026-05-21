<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('course')->latest()->paginate(15);
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::with('modules')->get();
        return view('quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Quiz::create($request->all());

        return redirect()->route('quizzes.index')->with('success', 'Kuis/Ujian berhasil dibuat.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        $uploadedVideos = \App\Models\Video::all();
        return view('quizzes.show', compact('quiz', 'uploadedVideos'));
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Kuis/Ujian berhasil dihapus.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate($this->getQuestionValidationRules($request));
        
        $type = $request->question_type;
        $options = null;
        $correctAnswer = '';

        if ($type === 'multiple_choice') {
            $options = [
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'true_false') {
            $options = [
                'A' => 'Benar',
                'B' => 'Salah',
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'short_answer') {
            $options = [
                'keywords' => $request->keywords,
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'fill_blank') {
            $options = [
                'code_template' => $request->code_template ?: $request->question,
                'blank_placeholder' => $request->blank_placeholder ?: '[blank]',
                'feedback_correct' => $request->feedback_correct,
                'feedback_incorrect' => $request->feedback_incorrect,
                'max_attempts' => $request->max_attempts ? intval($request->max_attempts) : 3,
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'reflection') {
            $correctAnswer = '';
        } elseif ($type === 'debugging') {
            $options = [
                'code_snippet' => $request->code_snippet ?: $request->question,
                'bug_description' => $request->bug_description ?: $request->question,
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'interactive_video') {
            $videoUrl = null;
            if ($request->hasFile('video_file')) {
                $file = $request->file('video_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('videos', $filename, 'public');
                $videoUrl = '/storage/' . $path;

                \App\Models\Video::create([
                    'module_id' => $quiz->module_id ?? null,
                    'title' => $file->getClientOriginalName(),
                    'video_path' => $videoUrl,
                    'duration' => 0,
                ]);
            } elseif (!empty($request->video_url_select)) {
                $videoUrl = $request->video_url_select;
            } elseif (!empty($request->video_url)) {
                $videoUrl = $request->video_url;
            }

            if (!$videoUrl) {
                return back()->withErrors(['video_url' => 'Pilih video terunggah, unggah video baru, atau masukkan URL video.'])->withInput();
            }

            $options = [
                'video_url' => $videoUrl,
                'timestamp' => intval($request->timestamp),
                'video_question_type' => $request->video_question_type,
            ];

            if ($request->video_question_type === 'multiple_choice') {
                $options['options'] = [
                    'A' => $request->option_a,
                    'B' => $request->option_b,
                    'C' => $request->option_c,
                    'D' => $request->option_d,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($request->video_question_type === 'true_false') {
                $options['options'] = [
                    'A' => 'Benar',
                    'B' => 'Salah',
                ];
                $correctAnswer = $request->correct_answer;
            } else {
                $correctAnswer = $request->correct_answer;
            }
        }

        $quiz->questions()->create([
            'question_type' => $type,
            'question' => $request->question,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'feedback' => $request->feedback,
            'points' => $request->points ?? 10,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan ke Kuis.');
    }

    public function edit(Quiz $quiz)
    {
        $courses = Course::with('modules')->get();
        return view('quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $quiz->update($request->all());

        // Sync the corresponding LearningActivity title/description and module if exists
        \App\Models\LearningActivity::where('quiz_id', $quiz->id)->update([
            'title' => 'Kuis: ' . $quiz->title,
            'description' => $quiz->description,
            'module_id' => $quiz->module_id,
        ]);

        return redirect()->route('quizzes.index')->with('success', 'Kuis/Ujian berhasil diperbarui.');
    }

    public function updateQuestion(Request $request, \App\Models\QuizQuestion $question)
    {
        try {
            $request->validate($this->getQuestionValidationRules($request));
            
            $type = $request->question_type;
            $options = null;
            $correctAnswer = '';

            if ($type === 'multiple_choice') {
                $options = [
                    'A' => $request->option_a,
                    'B' => $request->option_b,
                    'C' => $request->option_c,
                    'D' => $request->option_d,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($type === 'true_false') {
                $options = [
                    'A' => 'Benar',
                    'B' => 'Salah',
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($type === 'short_answer') {
                $options = [
                    'keywords' => $request->keywords,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($type === 'fill_blank') {
                $options = [
                    'code_template' => $request->code_template ?: $request->question,
                    'blank_placeholder' => $request->blank_placeholder ?: '[blank]',
                    'feedback_correct' => $request->feedback_correct,
                    'feedback_incorrect' => $request->feedback_incorrect,
                    'max_attempts' => $request->max_attempts ? intval($request->max_attempts) : 3,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($type === 'reflection') {
                $correctAnswer = '';
            } elseif ($type === 'debugging') {
                $options = [
                    'code_snippet' => $request->code_snippet ?: $request->question,
                    'bug_description' => $request->bug_description ?: $request->question,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($type === 'interactive_video') {
                $videoUrl = null;
                if ($request->hasFile('video_file')) {
                    $file = $request->file('video_file');
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('videos', $filename, 'public');
                    $videoUrl = '/storage/' . $path;

                    \App\Models\Video::create([
                        'module_id' => $question->quiz->module_id ?? null,
                        'title' => $file->getClientOriginalName(),
                        'video_path' => $videoUrl,
                        'duration' => 0,
                    ]);
                } elseif (!empty($request->video_url_select)) {
                    $videoUrl = $request->video_url_select;
                } elseif (!empty($request->video_url)) {
                    $videoUrl = $request->video_url;
                }

                if (!$videoUrl) {
                    if (isset($question->options['video_url'])) {
                        $videoUrl = $question->options['video_url'];
                    } else {
                        return back()->withErrors(['video_url' => 'Pilih video terunggah, unggah video baru, atau masukkan URL video.'])->withInput()->with('error_edit_question_id', $question->id);
                    }
                }

                $options = [
                    'video_url' => $videoUrl,
                    'timestamp' => intval($request->timestamp),
                    'video_question_type' => $request->video_question_type,
                ];

                if ($request->video_question_type === 'multiple_choice') {
                    $options['options'] = [
                        'A' => $request->option_a,
                        'B' => $request->option_b,
                        'C' => $request->option_c,
                        'D' => $request->option_d,
                    ];
                    $correctAnswer = $request->correct_answer;
                } elseif ($request->video_question_type === 'true_false') {
                    $options['options'] = [
                        'A' => 'Benar',
                        'B' => 'Salah',
                    ];
                    $correctAnswer = $request->correct_answer;
                } else {
                    $correctAnswer = $request->correct_answer;
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error_edit_question_id', $question->id);
        }

        $question->update([
            'question_type' => $type,
            'question' => $request->question,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'feedback' => $request->feedback,
            'points' => $request->points ?? 10,
        ]);

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroyQuestion(\App\Models\QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }

    private function getQuestionValidationRules(Request $request)
    {
        $rules = [
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,fill_blank,reflection,debugging,interactive_video',
            'question' => 'required|string',
            'points' => 'required|integer|min:1',
            'feedback' => 'nullable|string',
        ];

        $type = $request->question_type;

        if ($type === 'multiple_choice') {
            $rules['option_a'] = 'required|string';
            $rules['option_b'] = 'required|string';
            $rules['option_c'] = 'required|string';
            $rules['option_d'] = 'required|string';
            $rules['correct_answer'] = 'required|string|in:A,B,C,D';
        } elseif ($type === 'true_false') {
            $rules['correct_answer'] = 'required|string|in:A,B';
        } elseif ($type === 'short_answer') {
            $rules['correct_answer'] = 'required|string';
            $rules['keywords'] = 'nullable|string';
        } elseif ($type === 'fill_blank') {
            $rules['code_template'] = 'required|string';
            $rules['blank_placeholder'] = 'nullable|string';
            $rules['correct_answer'] = 'required|string';
            $rules['feedback_correct'] = 'nullable|string';
            $rules['feedback_incorrect'] = 'nullable|string';
            $rules['max_attempts'] = 'nullable|integer|min:1';
        } elseif ($type === 'reflection') {
            // reflection doesn't require correct_answer or options
        } elseif ($type === 'debugging') {
            $rules['code_snippet'] = 'required|string';
            $rules['bug_description'] = 'required|string';
            $rules['correct_answer'] = 'required|string';
        } elseif ($type === 'interactive_video') {
            $rules['timestamp'] = 'required|integer|min:0';
            $rules['video_question_type'] = 'required|in:multiple_choice,true_false,short_answer';
            $rules['video_file'] = 'nullable|file|mimes:mp4,mov,avi,wmv|max:102400';
            $rules['video_url_select'] = 'nullable|string';
            $rules['video_url'] = 'nullable|string';

            if ($request->video_question_type === 'multiple_choice') {
                $rules['option_a'] = 'required|string';
                $rules['option_b'] = 'required|string';
                $rules['option_c'] = 'required|string';
                $rules['option_d'] = 'required|string';
                $rules['correct_answer'] = 'required|string|in:A,B,C,D';
            } elseif ($request->video_question_type === 'true_false') {
                $rules['correct_answer'] = 'required|string|in:A,B';
            } else {
                $rules['correct_answer'] = 'required|string';
            }
        }

        return $rules;
    }
}
