<?php

namespace App\Models;

use App\Observers\ExpirationObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expiration extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates    = ['expiry_date'];

    protected static function boot()
    {
        parent::boot();
        Expiration::observe(ExpirationObserver::class) ;
    }

    public function setUserIdAttribute()
    {
        $this->attributes['user_id'] = auth('api')->id();
    }

    public function getExpirationImagesAttribute()
    {
        return $this->media()->where(['media_type' => 'image', 'option' => 'expiration_image'])->get();
    }

    public function getExpirationFilesAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'expiration_file'])->get();
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function usersfavorite()
    {
        return $this->belongsToMany(User::class, 'favorite_tenders');
    }
}
