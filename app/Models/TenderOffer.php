<?php

namespace App\Models;

use App\Observers\TenderOfferObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderOffer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        TenderOffer::observe(TenderOfferObserver::class) ;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTenderOfferImagesAttribute()
    {
        return $this->media()->where(['media_type' => 'image', 'option' => 'tender_offer_image'])->get();
    }

    public function getTenderOfferFilesAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'tender_offer_file'])->get();
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }
}
