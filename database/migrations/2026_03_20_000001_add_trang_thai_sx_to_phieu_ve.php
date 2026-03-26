<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('phieu_ve', function (Blueprint $table) {
            $table->enum('trang_thai_sx', ['chua_sx', 'dang_sx', 'hoan_thanh'])
                  ->default('chua_sx')
                  ->after('ma_lenh')
                  ->comment('Trạng thái sản xuất: chua_sx / dang_sx / hoan_thanh');
        });
    }

    public function down(): void
    {
        Schema::table('phieu_ve', function (Blueprint $table) {
            $table->dropColumn('trang_thai_sx');
        });
    }
};
