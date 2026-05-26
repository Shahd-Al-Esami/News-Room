<?php

use App\Jobs\PublishedArticleReportJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//send articles report for admins every week
Schedule::command('send:all-articles-published-report')->weeklyOn(1, '8:00');

//send articles report every friday
Schedule::command('articles:report')->fridays()->at('8:00');
//send articles report  on the first day of the month 
Schedule::command('articles:archive')->monthlyOn(1, '00:00');

//on server
//cd/path-to-project && php artisan schedule:run >> /dev/null 2>&1

