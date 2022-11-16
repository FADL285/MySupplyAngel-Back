<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMedia extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function app_mediaable()
    {
    	return $this->morphTo();
    }
}
