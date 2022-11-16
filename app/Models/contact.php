<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contact extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at', 'seleted_at'];

    protected $dates = ['read_at'];

    public function getImageAttribute()
    {
        $image = $this->user ? $this->user->avatar : asset('dashboardAssets/images/cover/cover_sm.png');
        return $image;
    }

    public function scopeReadMessages($query)
    {
        $query->whereNotNull('read_at');
    }

    public function scopeUnReadMessages($query)
    {
        $query->whereNull('read_at');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
