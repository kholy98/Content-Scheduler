<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_platform_settings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained()->onDelete('cascade');

            $table->unsignedInteger('characters_limit')->nullable();
            $table->boolean('allow_images')->default(true);       
            $table->boolean('allow_videos')->default(true);       

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_settings');
    }
};
