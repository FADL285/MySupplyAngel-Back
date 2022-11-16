<?php

namespace App\Models;

use App\Observers\TenderObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates    = ['expiry_date'];

    protected static function boot()
    {
        parent::boot();
        Tender::observe(TenderObserver::class) ;
    }

    public function setUserIdAttribute()
    {
        $this->attributes['user_id'] = auth('api')->id();
    }

    public function getTenderImagesAttribute()
    {
        return $this->media()->where(['media_type' => 'image', 'option' => 'tender_image'])->get();
    }

    public function getTenderOtherFilesAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'tender_other_file'])->get();
    }

    public function getTenderSpecificationsFileAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'tender_specifications_file'])->first();
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

    public function offers()
    {
        return $this->hasMany(TenderOffer::class);
    }

    public function usersfavorite()
    {
        return $this->belongsToMany(User::class, 'favorite_tenders');
    }
}
