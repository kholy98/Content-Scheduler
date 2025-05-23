<?php

namespace App\Http\Controllers;

use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $platforms = Platform::all();

        return response()->json([$platforms]);

        // Optional: show which platforms are active for the user
        //$userActivePlatformIds = $request->user()->platforms()->pluck('platform_id')->toArray();

        // return response()->json([
        //     'platforms' => $platforms->map(function ($platform) use ($userActivePlatformIds) {
        //         return [
        //             'id' => $platform->id,
        //             'name' => $platform->name,
        //             'type' => $platform->type,
        //             'active' => in_array($platform->id, $userActivePlatformIds),
        //         ];
        //     })
        // ]);
    }

}
