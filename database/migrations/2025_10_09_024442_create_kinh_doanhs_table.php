<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'sqlite_unipax';

    public function up(): void {
        Schema::connection($this->connection)->create('kinh_doanh', function (Blueprint $table) {
            $table->id();
            $table->string('ps');
            $table->integer('row');
            $table->string('ngayxuat')->nullable();
            $table->string('mahang')->nullable();
            $table->string('mau')->nullable();
            $table->string('size')->nullable();
            $table->string('mat')->nullable();
            $table->string('logo')->nullable();
            $table->integer('soluongdonhang')->nullable();
            $table->integer('sl_thuc')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::connection($this->connection)->dropIfExists('kinh_doanh');
    }
};
