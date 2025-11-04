<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'sqlite_unipax'; // dùng db riêng

    public function up(): void
    {
        Schema::connection($this->connection)->create('phieu_nhap', function (Blueprint $table) {
            $table->id();
            $table->string('ps');
            $table->integer('row_kd')->nullable();
            $table->string('mahang')->nullable();
            $table->string('size')->nullable();
            $table->string('mau')->nullable();
            $table->string('logo')->nullable();
            $table->integer('soluongdonhang')->nullable();
            $table->integer('sl_thuc')->nullable();
            $table->integer('dat')->default(0);
            $table->integer('loi')->default(0);
            $table->string('mat')->nullable();
            $table->string('ghichu')->nullable();
            $table->string('trangthai')->default('CHUA_DUYET');
            $table->date('ngayxuat')->nullable();
            $table->date('ngaynhap')->default(now());
            $table->string('nguoitao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('phieu_nhap');
    }
};
