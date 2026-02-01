<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('veikali', function (Blueprint $table) {
            $table->id('veikala_id');
            $table->string('nosaukums');
            $table->string('url')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
        });

        Schema::create('preces', function (Blueprint $table) {
            $table->id('preces_id');
            $table->string('nosaukums');
            $table->text('apraksts')->nullable();
            $table->string('razotajs')->nullable();
            $table->string('modelis')->nullable();
            $table->string('attels_url')->nullable();
            $table->foreignId('kategorijas_id')
                  ->constrained('kategorijas', 'kategorijas_id')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preces');
        Schema::dropIfExists('veikali');
    }
};
