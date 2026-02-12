<?php

namespace App\Notifications;

use App\Models\WorkingPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkingPaperApproved extends Notification
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
            ->subject("Working Paper Approved - {$this->workingPaper->financial_year}")
            ->greeting("Great News, {$notifiable->name}!")
            ->line("Your working paper for financial year **{$this->workingPaper->financial_year}** has been approved.")
            ->line("**Reviewed by:** {$this->workingPaper->reviewer->name}")
            ->line("**Approved on:** {$this->workingPaper->reviewed_at->format('M d, Y h:i A')}")
            ->action('View Working Paper', route('client.dashboard', ['year' => $this->workingPaper->financial_year]))
            ->line('Thank you for using our tax data capture system!');
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
            'status' => 'approved',
        ];
    }
}
