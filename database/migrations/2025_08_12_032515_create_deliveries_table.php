<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->nullable();
            $table->date('ngay_xuat')->nullable();
            $table->string('ma_hang')->nullable();
            $table->string('ps_code')->nullable();
            $table->string('size')->nullable();
            $table->date('ngay_gui_panel')->nullable();
            $table->string('so_phieu')->nullable();
            $table->string('sl_dat')->nullable();
            $table->string('sl_thuc_nhan')->nullable();
            $table->date('ngay_giao')->nullable();
            $table->string('sl_giao_dat')->nullable();
            $table->string('sl_giao_loi')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->string('panel')->nullable();
            $table->string('noi_giao')->nullable();
            $table->string('loai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
