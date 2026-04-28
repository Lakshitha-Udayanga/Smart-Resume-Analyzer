<?php

namespace App\Observers;

use App\Models\Job;

class JobObserver
{
    /**
     * Handle the Job "created" event.
     */
    public function created(\App\Models\Job $job): void
    {
        \App\Models\Notification::create([
            'type' => 'job',
            'notifiable_id' => $job->id,
            'title' => $job->title,
            'message' => 'New job opportunity from ' . ($job->company_name ?? 'Unknown Company'),
        ]);
    }

    /**
     * Handle the Job "updated" event.
     */
    public function updated(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "deleted" event.
     */
    public function deleted(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "restored" event.
     */
    public function restored(Job $job): void
    {
        //
    }

    /**
     * Handle the Job "force deleted" event.
     */
    public function forceDeleted(Job $job): void
    {
        //
    }
}
