<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['name', 'slug', 'desc'];

    protected static function boot()
    {
        parent::boot();
        Category::observe(CategoryObserver::class) ;
    }

    public function getImageAttribute()
    {
        $image = $this->media()->exists() ? 'storage/images/'.$this->media()->first()->media : 'dashboardAssets/images/banner/banner-2.jpg';

        return asset($image);
    }

    public function media()
    {
    	return $this->morphOne(AppMedia::class,'app_mediaable');
    }
}
