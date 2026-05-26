<?php
namespace App\Notifications\Implementation;

use App\Models\User;
use App\Notifications\AdminArticlePublishedNotification;
use App\Notifications\Contracts\NotificationServiceInterface;

class DatabaseNotification implements NotificationServiceInterface
{
    public function send($userId, array $data)
    {
        $user=User::findOrFail($userId); // Assuming you have a User model and it has an email field
        $user->notify(new AdminArticlePublishedNotification($data)); // Assuming you have a User model and it uses the Notifiable trait
        // Simulate saving a notification to the database
        // echo "Notification saved for user {$userId} with message: {$data['title']}";
    }
}
