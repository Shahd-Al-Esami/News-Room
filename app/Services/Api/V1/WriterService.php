<?php
namespace App\Services\Api\V1;

use App\Notifications\Contracts\NotificationServiceInterface;

class WriterService
{
  public function __construct(private NotificationServiceInterface $notification)
{
}


public function notify(int $userId, array $data)
{
    $this->notification->send($userId, $data);

}
}
