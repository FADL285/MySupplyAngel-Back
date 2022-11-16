<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Category;
use Illuminate\Support\Facades\File;

class CategoryObserver
{
    public function saved(Category $category)
    {
        if (request()->hasFile('image')) {
            if ($category->media()->exists()) {
                $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Category', 'app_mediaable_id' => $category->id , 'media_type' => 'image'])->first();

                if (file_exists(storage_path('app/public/images/'.$image->media))){
                    File::delete(storage_path('app/public/images/'.$image->media));
                }
                
                $image->delete();
            }

            $image = request()->file('image')->store('/categories', 'uploads');
            $category->media()->create(['media' => $image,'media_type' => 'image']);
        }
    }

    public function deleted(Category $category)
    {
        if ($category->media()->exists()) {
            $image = AppMedia::where(['app_mediaable_type' => 'App\Models\Category', 'app_mediaable_id' => $category->id , 'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/'.$image->media))){
                File::delete(storage_path('app/public/images/'.$image->media));
            }
            $image->delete();
        }
    }
}
