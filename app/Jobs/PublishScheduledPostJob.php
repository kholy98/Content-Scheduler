<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class PublishScheduledPostJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post->load('platforms'); // Eager load platforms
    }

    public function handle(): void
    {
        $this->post->update(['status' => 'published']);

        Log::info("Post ID {$this->post->id} status updated to published.");

        foreach ($this->post->platforms as $platform) {
            $this->post->platforms()->updateExistingPivot($platform->id, [
                'platform_status' => 'posted',
            ]);
        }
    }
}
