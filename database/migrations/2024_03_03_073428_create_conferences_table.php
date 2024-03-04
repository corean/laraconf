<?php

use App\Models\Venue;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status');
            $table->string('region');
            $table->boolean('is_published')->default(false);
            $table->foreignIdFor(Venue::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
