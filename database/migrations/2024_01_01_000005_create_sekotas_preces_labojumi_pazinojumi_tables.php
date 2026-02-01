<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sekotas_preces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lietotaja_id')
                  ->constrained('lietotaji', 'lietotaja_id')
                  ->onDelete('cascade');
            $table->foreignId('preces_id')
                  ->constrained('preces', 'preces_id')
                  ->onDelete('cascade');
            $table->decimal('target_price', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['lietotaja_id', 'preces_id']);
        });

        Schema::create('labojumi', function (Blueprint $table) {
            $table->id('labojuma_id');
            $table->foreignId('lietotaja_id')
                  ->constrained('lietotaji', 'lietotaja_id')
                  ->onDelete('cascade');
            $table->foreignId('preces_id')
                  ->constrained('preces', 'preces_id')
                  ->onDelete('cascade');
            $table->text('labojuma_teksts');
            $table->enum('statuss', ['iesniegts', 'apstiprināts', 'noraidīts'])
                  ->default('iesniegts');
            $table->foreignId('apstiprinaja_id')
                  ->nullable()
                  ->constrained('lietotaji', 'lietotaja_id')
                  ->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('pazinojumi', function (Blueprint $table) {
            $table->id('pazinojuma_id');
            $table->foreignId('lietotaja_id')
                  ->constrained('lietotaji', 'lietotaja_id')
                  ->onDelete('cascade');
            $table->foreignId('cenas_id')
                  ->nullable()
                  ->constrained('cenu_vesture', 'cenas_id')
                  ->onDelete('cascade');
            $table->string('zinojums');
            $table->boolean('izlasits')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pazinojumi');
        Schema::dropIfExists('labojumi');
        Schema::dropIfExists('sekotas_preces');
    }
};
