<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrintLogsTable extends Migration
{
    public function up()
    {
        Schema::create('print_logs', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_name'); // Tên sheet in
            $table->string('printed_by'); // Người in
            $table->timestamp('printed_at'); // Thời gian in
            $table->string('pdf_path')->nullable(); // Đường dẫn PDF
            
            // Duyệt & ký
            $table->boolean('is_approved')->default(false); // Đã duyệt?
            $table->string('approved_by')->nullable(); // Sếp duyệt
            $table->text('signature')->nullable(); // Chữ ký
            $table->timestamp('approved_at')->nullable(); // Thời gian duyệt
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('print_logs');
    }
}
