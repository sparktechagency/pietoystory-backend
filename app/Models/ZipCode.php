<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZipCode extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function county()
    {
        return $this->belongsTo(County::class);
    }
}
