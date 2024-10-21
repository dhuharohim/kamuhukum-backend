<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $article;
    protected $comment;
    protected $commentFor;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, $comment, $commentFor)
    {
        $this->article = $article;
        $this->comment = $comment;
        $this->commentFor = $commentFor;
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
        return (new MailMessage)
            ->subject('New Comment on Article')
            ->view('vendor.mail.editor.new_comment', [
                'article' => $this->article,
                'comment' => $this->comment,
                'commentFor' => $this->commentFor
            ]);
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
            'article_title' => $this->article->title
        ];
    }
}
