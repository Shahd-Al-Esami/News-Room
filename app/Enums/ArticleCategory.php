<?php
namespace App\Enums;
enum ArticleCategory: string
{
    case General = 'general';
    case Technology = 'technology';
    case Health = 'health';
    case Sports = 'sports';
    case Entertainment = 'entertainment';
    case Business = 'business';
    case Politics = 'politics';
    case Science = 'science';
    case World = 'world';
    case Lifestyle = 'lifestyle';
    case Travel = 'travel';
}
