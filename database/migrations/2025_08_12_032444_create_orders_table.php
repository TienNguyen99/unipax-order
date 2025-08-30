<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id'); // số thứ tự trong PS
            $table->string('ma_hang')->nullable();
            $table->string('ps_code')->nullable();
            $table->string('mau_vai')->nullable();
            $table->string('mau_logo')->nullable();
            $table->string('panel')->nullable();
            $table->date('ngay_xuat')->nullable();
            $table->string('po_number')->nullable();
            $table->string('size')->nullable();
            $table->integer('sl_dat')->nullable();
            $table->string('don_vi_tinh')->nullable();
            $table->string('mua')->nullable();
            $table->decimal('gia', 15, 2)->nullable();
            $table->string('ten_hang')->nullable();
            $table->string('ma_lenh')->nullable();
            $table->decimal('tong_tien', 15, 2)->nullable();
            $table->integer('hang_ve')->nullable();
            $table->integer('da_giao')->nullable();
            $table->integer('con_lai')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
