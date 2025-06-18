<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    public static function addSecondsToQueue()
    {
        $job = Job::orderBy('available_at', 'desc')->first();
        if ($job) {
            $now          = Carbon::now()->timestamp;
            $jobTimestamp = $job->available_at + env('MAIL_DELAY_IN_SECONDS', 4);
            $result       = $jobTimestamp - $now;

            return $result;
        } else {
            return 0;
        }
    }

}
