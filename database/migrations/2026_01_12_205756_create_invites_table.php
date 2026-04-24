php artisan db:wipe && php artisan migrate<?php

use App\Models\Team;
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
    Schema::create('invites', function (Blueprint $table) {
        $table->uuid('id')->primary();
        
        $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        
        $table->foreignUuid('team_id')->constrained()->cascadeOnDelete();
        
        $table->foreignUuid('inviter_id')->constrained('users')->cascadeOnDelete();
        
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
