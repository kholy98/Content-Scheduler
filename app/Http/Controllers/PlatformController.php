<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $platforms = Platform::with('setting')->select('id', 'name', 'type')->get();

        return $platforms;
    }




    public function update(Request $request, Platform $platform)
    {
        // Ensure the user is an admin
        if (!Auth::user() || !Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        
        $validated = $request->validate([
            'characters_limit' => 'required|integer|min:0',
        ]);

        
        $platform->setting()->updateOrCreate(
            ['platform_id' => $platform->id],
            ['characters_limit' => $validated['characters_limit']]
        );

        return response()->json(['message' => 'Platform settings updated successfully.']);
    }

    public function setPlatformStatus(Request $request, User $user, Platform $platform)
    {
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if(!$authUser->is_admin){
            return response()->json(['message' => 'Only admins can update platform status'], 403);
        }

        if ($user->is_admin) {
            return response()->json(['message' => 'Cannot modify other admins'], 403);
        }

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        // Attach or update pivot table with is_active
        $user->platforms()->syncWithoutDetaching([
            $platform->id => ['is_active' => $validated['is_active']],
        ]);

        $status = $validated['is_active'] ? 'activated' : 'deactivated';

        return response()->json([
            'message' => "Platform {$status} for user.",
            'is_active' => $validated['is_active'],
        ]);
    }

}
