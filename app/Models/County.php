<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function zipCodes()
    {
        return $this->hasMany(ZipCode::class);
    }
}
