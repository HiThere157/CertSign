<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'created_by_id',
        'owner_id',
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

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id')->withTrashed();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id')->withTrashed();
    }

    public function issuer()
    {
        return $this->belongsTo(Certificate::class, 'issuer_id')->withTrashed();
    }

    public function encryptionKey()
    {
        return $this->hasOne(EncryptionKey::class, 'certificate_id', 'id')->withTrashed();
    }
}
