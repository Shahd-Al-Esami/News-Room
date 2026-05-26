<?php
namespace App\Notifications\Implementation;
use App\Mail\WriterArticlePublishedMail;
use App\Models\User;
use App\Notifications\Contracts\NotificationServiceInterface;
use Illuminate\Support\Facades\Mail;

class EmailNotification implements NotificationServiceInterface
{
    public function send($userId,$data)
    {
        $user = User::findOrFail($userId);
        Mail::to($user->email)->send(new WriterArticlePublishedMail($data));
        // Simulate sending an email notification
        // echo "Email sent to user {$userId} with message: {$message}";
    }
}
