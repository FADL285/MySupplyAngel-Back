<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Expiration;
use Illuminate\Support\Facades\File;

class ExpirationObserver
{
    public function saved(Expiration $expiration)
    {
        if (request()->hasFile('expiration_images'))
        {
            foreach (request()->file('expiration_images') as $image)
            {
                $img = $image->store('/expirations', 'uploads');
                $expiration->media()->create(['media' => $img,'media_type' => 'image', 'option' => 'expiration_image']);
            }
        }

        if (request()->hasFile('expiration_files'))
        {
            foreach (request()->file('expiration_files') as $file)
            {
                $file = $file->store('/expirations', 'uploads');
                $expiration->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'expiration_file']);
            }
        }
    }

    public function deleted(Expiration $expiration)
    {
        if ($expiration->media()->exists()) {
            $medias = AppMedia::where(['app_mediaable_type' => 'App\Models\Expiration', 'app_mediaable_id' => $expiration->id])->get();
            foreach ($medias as $media)
            {
                if (file_exists(storage_path('app/public/images/'.$media->media))){
                    File::delete(storage_path('app/public/images/'.$media->media));
                }
            }
            $medias->each->delete();
        }
    }
}
