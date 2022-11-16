<?php

namespace App\Models;

use App\Observers\CountryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Country extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name', 'nationality'];

    protected static function boot()
    {
        parent::boot();
        Country::observe(CountryObserver::class) ;
    }

    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/'.$this->media()->first()->media : 'dashboardAssets/images/cover/cover_sm.png';

        return asset($image);
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }
}
