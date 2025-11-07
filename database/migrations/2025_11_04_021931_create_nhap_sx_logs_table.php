<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nhap_sx_logs', function (Blueprint $table) {
            $table->id();
            $table->date('ngay_nhap')->nullable();
            $table->string('lenh_sx')->nullable();
            $table->string('cong_doan')->nullable();
            $table->string('may_sx')->nullable();
            $table->integer('gio_sx')->nullable();
            $table->integer('so_pick')->nullable();
            $table->integer('so_cuon')->nullable();
            $table->integer('so_dong')->nullable();
            $table->integer('so_ban')->nullable();
            $table->integer('so_dau')->nullable();
            $table->integer('so_khuon')->nullable();
            $table->string('khuon_sx')->nullable();
            $table->integer('so_luong_dat');
            $table->integer('so_luong_loi')->nullable();
            $table->text('dien_giai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhap_sx_logs');
    }
};
