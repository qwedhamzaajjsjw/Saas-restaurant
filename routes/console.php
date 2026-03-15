<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
| يتطلب هذا cron entry واحد فقط على السيرفر:
| * * * * * cd /var/www/restaurant && php artisan schedule:run >> /dev/null 2>&1
*/

// تحقق يومي من الاشتراكات المنتهية وتحديث حالتها
Schedule::command('subscription:expire')->dailyAt('00:05');

// تنظيف الـ sessions القديمة (إذا كان driver = database)
Schedule::command('session:gc')->daily();
