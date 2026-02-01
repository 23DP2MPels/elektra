<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specifikacijas', function (Blueprint $table) {
            $table->id('specifikacijas_id');
            $table->foreignId('preces_id')
                  ->constrained('preces', 'preces_id')
                  ->onDelete('cascade');
            $table->string('parametrs');
            $table->string('vertiba');
            $table->timestamps();
        });

        Schema::create('cenu_vesture', function (Blueprint $table) {
            $table->id('cenas_id');
            $table->foreignId('preces_id')
                  ->constrained('preces', 'preces_id')
                  ->onDelete('cascade');
            $table->foreignId('veikala_id')
                  ->constrained('veikali', 'veikala_id')
                  ->onDelete('cascade');
            $table->decimal('cena', 10, 2);
            $table->decimal('iepriekšējā_cena', 10, 2)->nullable();
            $table->string('url')->nullable();
            $table->boolean('pieejams')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cenu_vesture');
        Schema::dropIfExists('specifikacijas');
    }
};
