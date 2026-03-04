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
        Schema::create('phieu_xuat_kho', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu', 100)->unique()->comment('Mã phiếu xuất kho: PXK-YYYYMMDD-XXXX');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Người tạo phiếu');
            $table->date('ngay_xuat')->comment('Ngày xuất kho');
            $table->enum('trang_thai', ['draft', 'confirmed', 'completed', 'cancelled'])
                ->default('confirmed')
                ->comment('Trạng thái phiếu');
            $table->integer('tong_so_items')->default(0)->comment('Tổng số items trong phiếu');
            $table->text('ghi_chu')->nullable()->comment('Ghi chú chung');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('ma_phieu');
            $table->index('ngay_xuat');
            $table->index('trang_thai');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_xuat_kho');
    }
};
