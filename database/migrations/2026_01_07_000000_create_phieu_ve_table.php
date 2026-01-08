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
        Schema::create('phieu_ve', function (Blueprint $table) {
            $table->id();
            $table->string('export_date', 50)->nullable();
            $table->string('phieu_ps', 100)->nullable();
            $table->string('kich_thuoc', 100)->nullable();
            $table->string('mau_vai', 100)->nullable();
            $table->string('mau_logo', 100)->nullable();
            $table->string('ngay_nhan_panel', 50)->nullable();
            $table->string('so_phieu', 100)->nullable();
            $table->string('ma_hang', 100)->nullable();
            $table->string('ma_lenh', 100)->nullable();
            $table->string('front_dat', 50)->nullable();
            $table->string('front_loi', 50)->nullable();
            $table->string('back_dat', 50)->nullable();
            $table->string('back_loi', 50)->nullable();
            $table->string('vi_tri', 100)->nullable();
            $table->string('ngay', 50)->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('so_luong_nhan', 50)->nullable();
            $table->string('noi_giao', 255)->nullable();
            $table->string('ngay_xuat_kho', 50)->nullable();
            $table->string('so_luong_donhang', 50)->nullable();
            $table->timestamp('ngay_nhan')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_ve');
    }
};
