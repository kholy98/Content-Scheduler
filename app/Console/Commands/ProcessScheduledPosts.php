<?php

namespace App\Console\Commands;

use App\Jobs\PublishScheduledPostJob;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-posts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $duePosts = Post::where('status', 'scheduled')
            ->where('scheduled_time', '<=', now())
            ->get();
        
        Log::info($duePosts);

        foreach ($duePosts as $post) {
            dispatch_sync(new PublishScheduledPostJob($post));
        }
    }
}
