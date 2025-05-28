<?php

namespace App\Http\Controllers;

use App\Models\Platform;
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

}
