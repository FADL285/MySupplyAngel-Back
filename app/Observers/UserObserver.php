<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\User;
use Illuminate\Support\Facades\File;

class UserObserver
{
    public function saved(User $user)
    {
        if (request()->hasFile('avatar')) {
            if ($user->media()->exists()) {
                $avatar = AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id , 'media_type' => 'image', 'options' => 'avatar'])->first();

                if (file_exists(storage_path('app/public/images/'.$avatar->media))){
                    File::delete(storage_path('app/public/images/'.$avatar->media));
                }

                $avatar->delete();
            }

            $avatar = request()->file('image')->store('/users', 'uploads');
            $user->media()->create(['media' => $avatar, 'media_type' => 'image', 'options' => 'avatar']);
        }
    }

    public function deleted(User $user)
    {
        if ($user->media()->exists()) {
            $avatar = AppMedia::where(['app_mediaable_type' => 'App\Models\User', 'app_mediaable_id' => $user->id , 'media_type' => 'image'])->first();
            if (file_exists(storage_path('app/public/images/'.$avatar->media))){
                File::delete(storage_path('app/public/images/'.$avatar->media));
            }
            $avatar->delete();
        }
    }
}
