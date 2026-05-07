<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionReceivedNotification extends Notification
{
    use Queueable;

    public $submission;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'submission',
            'title' => 'Pengumpulan Baru: ' . $this->submission->assignment->title,
            'message' => $this->submission->user->name . ' telah mengumpulkan tugas.',
            'url' => route('assignments.submissions', $this->submission->assignment_id),
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'color' => 'emerald'
        ];
    }
}
