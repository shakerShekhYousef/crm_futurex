<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateClient extends Model
{
    protected $fillable = [
        'name', 'email', 'phone','company_name','workspace'
    ];

    public function opportunities()
    {
        return $this->hasMany(Opportunity::class, 'candidate_client_id','id');
    }
}
