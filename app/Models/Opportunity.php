<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = [
        'candidate_client_id',
        'follow_up_date',
        'status',
        'contact_method',
        'future_notes',
        'current_notes',
        'next_follow_up_date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
