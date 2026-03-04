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
        Schema::create('phieu_xuat_kho_chi_tiet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phieu_xuat_kho_id')->comment('ID phiếu xuất kho header');
            $table->unsignedBigInteger('phieu_ve_id')->comment('ID phiếu về');
            
            // Snapshot data tại thời điểm xuất kho (để tránh mất dữ liệu khi phieu_ve bị sửa/xóa)
            $table->string('phieu_ps', 100)->nullable();
            $table->string('ma_hang', 100)->nullable();
            $table->string('ma_lenh', 100)->nullable();
            $table->string('kich_thuoc', 100)->nullable();
            $table->string('vi_tri', 100)->nullable();
            $table->string('so_luong_donhang', 50)->nullable();
            $table->string('so_luong_nhan', 50)->nullable();
            
            // Dữ liệu nhập liệu
            $table->string('makhac_dat', 50)->nullable();
            $table->string('makhac_loi', 50)->nullable();
            $table->string('front_dat', 50)->nullable();
            $table->string('front_loi', 50)->nullable();
            $table->string('back_dat', 50)->nullable();
            $table->string('back_loi', 50)->nullable();
            $table->text('ghi_chu')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('phieu_xuat_kho_id')
                ->references('id')
                ->on('phieu_xuat_kho')
                ->onDelete('cascade');
                
            $table->foreign('phieu_ve_id')
                ->references('id')
                ->on('phieu_ve')
                ->onDelete('cascade');
            
            // Indexes
            $table->index('phieu_xuat_kho_id');
            $table->index('phieu_ve_id');
            $table->index('phieu_ps');
            $table->index('ma_hang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_xuat_kho_chi_tiet');
    }
};
