<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:fetch-movie')->everyFifteenMinutes();
Schedule::command('app:fetch-genres')->everyFifteenMinutes();
