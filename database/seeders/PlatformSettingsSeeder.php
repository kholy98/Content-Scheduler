<?php

namespace Database\Seeders;

use App\Models\Platform;
use App\Models\PlatformSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlatformSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $platforms = Platform::all();

        foreach ($platforms as $platform) {
            PlatformSetting::create([
                'platform_id' => $platform->id,
                'characters_limit' => match ($platform->type) {
                    'twitter' => 280,
                    'instagram' => 2200,
                    'linkedin' => 1300,
                    default => null,
                },
                'allow_images' => true,
                'allow_videos' => true,
            ]);
        }
    }
}
