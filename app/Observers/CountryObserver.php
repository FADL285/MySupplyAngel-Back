<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Country;
use Illuminate\Support\Facades\File;

class CountryObserver
{
    public function saved(Country $country)
    {
        if (request()->hasFile('image')) {
            if ($country->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Country', 'app_mediaable_id' => $country->id , 'media_type' => 'image'])->first();

                if (file_exists(storage_path('app/public/images/'.$image->media))){
                    File::delete(storage_path('app/public/images/'.$image->media));
                }

                $image->delete();
            }

            $image = request()->file('image')->store('/countries', 'uploads');
            $country->media()->create(['media' => $image,'media_type' => 'image']);
        }
    }

    public function deleted(Country $country)
    {
        if ($country->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Country', 'app_mediaable_id' => $country->id , 'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/'.$image->media))){
                File::delete(storage_path('app/public/images/'.$image->media));
            }
            $image->delete();
        }
    }
}
