<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncryptionKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_id',
        'key',
    ];
}
