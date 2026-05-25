<?php
namespace App\Enums;
enum UserRoles: string
{
    case ADMIN = 'admin';
    case Writer= 'writer';
    case Reader= 'reader';
}
