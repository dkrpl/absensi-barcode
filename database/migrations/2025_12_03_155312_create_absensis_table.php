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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_shift')->constrained('shifts')->onDelete('cascade');
            $table->foreignId('id_barcode')->nullable()->constrained('barcodes')->nullOnDelete();
            $table->date('tanggal_absen');
            $table->dateTime('waktu_absen');
            $table->enum('status', ['hadir', 'terlambat']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
