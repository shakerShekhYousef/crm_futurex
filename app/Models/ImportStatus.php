<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportStatus extends Model
{
    protected $fillable = [
        'file_name',
        'total_rows',
        'processed_rows',
        'status',
        'error'
    ];

    protected $casts = [
        'processed_rows' => 'integer',
        'total_rows' => 'integer'
    ];
}
