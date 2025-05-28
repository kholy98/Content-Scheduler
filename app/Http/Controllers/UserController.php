<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::select('id', 'name', 'email', 'is_admin')->get();
    }

    public function getUserPlatforms(User $user)
    {
        $platforms = Platform::select('id', 'name', 'type')
            ->get()
            ->map(function ($platform) use ($user) {
                $pivot = $user->platforms()->where('platform_id', $platform->id)->first();
                $platform->is_active = optional($pivot)->pivot->is_active ?? false;
                return $platform;
            });

        return response()->json(['platforms' => $platforms]);
    }
}
