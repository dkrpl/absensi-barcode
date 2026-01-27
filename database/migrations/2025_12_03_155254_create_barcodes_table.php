<?php

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
       Schema::create('barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_shift')->constrained('shifts')->onDelete('cascade');
            $table->uuid('kode_barcode')->unique();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_akhir');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodes');
    }
};
