<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lietotaji', function (Blueprint $table) {
            $table->id('lietotaja_id');
            $table->string('vards')->nullable();
            $table->string('epasts')->unique();
            $table->string('parole');
            $table->enum('loma', ['viesis', 'registrets_lietotajs', 'redaktors', 'administrators'])
                  ->default('registrets_lietotajs');
            $table->boolean('sanemt_pazinojumus')->default(true);
            $table->integer('tiesibu_limenis')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lietotaji');
    }
};
