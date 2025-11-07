<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLenhSxTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::create('lenh_sx', function (Blueprint $table) {
        $table->id();
        $table->string('ma_lenh')->unique();           // Mã lệnh SX
        $table->string('po')->nullable();              // Purchase Order
        $table->string('nhan_vien_theo_doi')->nullable();
        $table->string('khach_hang')->nullable();
        $table->string('model_code')->nullable();
        $table->string('item_code')->nullable();
        $table->string('description')->nullable();
        $table->string('size')->nullable();
        $table->string('color')->nullable();
        $table->string('unit')->nullable();
        $table->integer('so_luong_dat')->nullable();
        $table->decimal('don_gia', 15, 2)->nullable();
        $table->date('ngay_nhan')->nullable();
        $table->date('ngay_hen_giao')->nullable();
        $table->date('ngay_khach_yeu_cau')->nullable();
        $table->string('noi_giao')->nullable();
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('lenh_sx');
    }
}
