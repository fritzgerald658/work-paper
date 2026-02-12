<?php

namespace App\Notifications;

use App\Models\WorkingPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkingPaperRejected extends Notification
{
    use Queueable;

    public $workingPaper;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkingPaper $workingPaper)
    {
        $this->workingPaper = $workingPaper;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Working Paper Needs Revision - {$this->workingPaper->financial_year}")
            ->error()
            ->greeting("Hello {$notifiable->name},")
            ->line("Your working paper for financial year **{$this->workingPaper->financial_year}** requires revision before it can be approved.")
            ->line("**Reviewed by:** {$this->workingPaper->reviewer->name}")
            ->line("**Feedback:**")
            ->line($this->workingPaper->admin_comment)
            ->line("Please log in to your account, review the feedback, make the necessary corrections, and resubmit your working paper.")
            ->action('Review & Resubmit', route('client.dashboard', ['year' => $this->workingPaper->financial_year]))
            ->line('If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'working_paper_id' => $this->workingPaper->id,
            'financial_year' => $this->workingPaper->financial_year,
            'status' => 'rejected',
            'admin_comment' => $this->workingPaper->admin_comment,
        ];
    }
}
