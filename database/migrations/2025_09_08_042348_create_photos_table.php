<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->nullable()
                  ->constrained('albums')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('image'); 
            $table->unsignedInteger('display_order')->default(0);
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['album_id','display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
