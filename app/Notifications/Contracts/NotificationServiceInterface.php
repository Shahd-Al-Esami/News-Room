<?php
namespace App\Notifications\Contracts;

interface NotificationServiceInterface
{
    public function send($userId,array $data);
}
