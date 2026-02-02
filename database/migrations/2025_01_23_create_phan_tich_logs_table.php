<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('phan_tich_logs', function (Blueprint $table) {
            $table->id();
            $table->string('so_phieu')->nullable(); // Số phiếu (liên kết với nhap_sx_logs)
            $table->string('lenh_sx')->nullable(); // Mã lệnh sản xuất
            $table->string('nhan_vien_id')->nullable(); // Mã nhân viên phân tích
            $table->datetime('ngay_nhap')->nullable(); // Ngày nhập
            
            // Chi tiết nguyên liệu (JSON hoặc separate table)
            $table->json('ingredients')->nullable(); // [{material_name, material_unit, definition_unit}, ...]
            
            $table->text('dien_giai')->nullable(); // Ghi chú
            $table->boolean('da_in')->default(false)->nullable(); // Đã in hay chưa
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_tich_logs');
    }
};
