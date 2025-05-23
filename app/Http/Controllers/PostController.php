<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->from);
        }
    
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->to);
        }

        // search by title/content
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        
        $posts = $query->latest()->paginate(10);

        return response()->json($posts);
    }

}
