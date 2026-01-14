<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintLog extends Model
{
    use HasFactory;

    protected $table = 'print_logs';

    protected $fillable = [
        'sheet_name',
        'printed_by',
        'printed_at',
        'pdf_path',
        'is_approved',
        'approved_by',
        'signature',
        'approved_at',
    ];

    protected $dates = [
        'printed_at',
        'approved_at',
    ];
}
