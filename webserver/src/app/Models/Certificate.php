<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by_id',
        'valid_from',
        'valid_to',
        'issuer_id',
        'serial_number',
        'self_signed',
    ];

    public function daysValid()
    {
        return floor((strtotime($this->valid_to) - time()) / 86400);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function issuer()
    {
        return $this->belongsTo(Certificate::class, 'issuer_id');
    }
}
