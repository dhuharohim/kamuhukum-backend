<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\View;

class EditorAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $article;
    protected $editor;
    protected $attachments;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, $editor, $attachments = [])
    {
        $this->article = $article;
        $this->editor = $editor;
        $this->attachments = $attachments;
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
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('New Article Assigned for Editing')
            ->view('vendor.mail.editor.assigned', [
                'article' => $this->article,
                'editor' => $this->editor
            ]);

        foreach ($this->attachments as $attachment) {
            $mailMessage->attach($attachment['path'], [
                'as' => $attachment['name'],
                'mime' => $attachment['mime'],
            ]);
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'article_title' => $this->article->title,
            'editor_id' => $this->editor->id,
            'editor_name' => $this->editor->username,
            'attachments' => $this->attachments,
        ];
    }
}
