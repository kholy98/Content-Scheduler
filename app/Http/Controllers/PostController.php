<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image_url' => 'nullable|url',
            'scheduled_time' => 'nullable|date|after:now',
            'platforms' => 'required|array',
            'platforms.*' => [
                'required',
                Rule::in(['twitter', 'instagram', 'linkedin']),
            ],
        ]);

        foreach ($request->platforms as $platform) {
            if ($platform === 'twitter' && strlen($request->content) > 280) {
                return response()->json([
                    'error' => 'Content exceeds Twitter 280 character limit.'
                ], 422);
            }
        }

        //Limit 10 shceduled posts per day
        $scheduledDate = Carbon::parse($request->scheduled_time)->toDateString();

        $existingScheduledCount = Post::where('user_id', Auth::id())
            ->where('status', 'scheduled')
            ->whereDate('scheduled_time', $scheduledDate)
            ->count();
        
        if ($request->scheduled_time && $existingScheduledCount >= 10) {
            return response()->json([
                'error' => 'You have reached the limit of 10 scheduled posts for ' . $scheduledDate
            ], 429);
        }

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image_url' => $request->image_url,
            'scheduled_time' => $request->scheduled_time,
            'status' => $request->scheduled_time ? 'scheduled' : 'published',
            'user_id' => Auth::id(),
        ]);

        $platformIds = Platform::whereIn('type', $request->platforms)->pluck('id');

        $pivotData = [];
        $status = $request->scheduled_time ? 'pending' : 'posted';

        foreach ($platformIds as $id) {
            $pivotData[$id] = ['platform_status' => $status];
        }

        $post->platforms()->attach($pivotData);


        return response()->json([
            'message' => 'Post created successfully.',
            'post' => $post->load('platforms'),
        ], 201);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Post::with('platforms')
            ->where('user_id', $user->id);

        // Filter by status
        if ($request->status != null) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->from != null) {
            $query->where('created_at', '>=', $request->from);
        }
    
        if ($request->to != null) {
            $query->where('created_at', '<=', $request->to);
        }

        // search by title/content
        if ($request->search != null) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        
        $posts = $query->latest()->paginate(10);

        return response()->json($posts);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }
    
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        if ($post->status !== 'scheduled') {
            return response()->json(['error' => 'Only scheduled posts can be updated.'], 400);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image_url' => 'nullable|string',
            'scheduled_time' => 'nullable|date|after:now',
            'platforms' => 'nullable|array',
            'platforms.*' => ['required', Rule::in(['twitter', 'instagram', 'linkedin'])],
        ]);

        
        if (in_array('twitter', $request->platforms ?? []) && strlen($request->content ?? $post->content) > 280) {
            return response()->json(['error' => 'Content exceeds Twitter 280 character limit.'], 422);
        }

        
        $post->update($request->only('title', 'content', 'image_url', 'scheduled_time'));

        // Sync platforms if provided
        if ($request->has('platforms')) {
            $platformIds = Platform::whereIn('type', $request->platforms)->pluck('id');
            $post->platforms()->syncWithPivotValues($platformIds, [
                'platform_status' => 'pending'
            ]);
        }

        return response()->json(['message' => 'Post updated successfully', 'post' => $post->load('platforms')]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
    
        if (!$post) {
            return response()->json(['error' => 'Post not found.'], 404);
        }
    
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
    
        $post->platforms()->detach();
        $post->delete();
    
        return response()->json(['message' => 'Post deleted successfully.']);
    }



    public function dashboardData()
    {
        $user = Auth::user();
    
        // Fetch all posts with platforms for the user
        $posts = Post::with('platforms')->where('user_id', $user->id)->latest()->get();
    
        $total = $posts->count();
        $published = $posts->where('status', 'published')->count();
        $scheduled = $posts->where('status', 'scheduled')->count();
        $successRate = $total ? round(($published / $total) * 100, 1) : 0;
    
        // Get post count per platform type
        $postsPerPlatform = DB::table('post_platform')
            ->join('platforms', 'post_platform.platform_id', '=', 'platforms.id')
            ->join('posts', 'post_platform.post_id', '=', 'posts.id')
            ->where('posts.user_id', $user->id)
            ->select('platforms.type', DB::raw('count(*) as count'))
            ->groupBy('platforms.type')
            ->get();
    
        return response()->json([
            'posts' => $posts,
            'total' => $total,
            'published' => $published,
            'scheduled' => $scheduled,
            'success_rate' => $successRate,
            'posts_per_platform' => $postsPerPlatform,
        ]);
    }
    


}
