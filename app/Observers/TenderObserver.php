<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\Tender;
use Illuminate\Support\Facades\File;

class TenderObserver
{
    public function saved(Tender $tender)
    {
        if (request()->hasFile('tender_images'))
        {
            foreach (request()->file('tender_images') as $image)
            {
                $img = $image->store('/tenders', 'uploads');
                $tender->media()->create(['media' => $img,'media_type' => 'image', 'option' => 'tender_image']);
            }
        }

        if (request()->hasFile('tender_other_files'))
        {
            foreach (request()->file('tender_other_files') as $file)
            {
                $file = $file->store('/tenders', 'uploads');
                $tender->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'tender_other_file']);
            }
        }

        if (request()->hasFile('tender_specifications_file')) {
            $file = request()->file('tender_specifications_file')->store('/tenders', 'uploads');
            $tender->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'tender_specifications_file']);
        }
    }

    public function deleted(Tender $tender)
    {
        if ($tender->media()->exists()) {
            $medias = AppMedia::where(['app_mediaable_type' => 'App\Models\Tender', 'app_mediaable_id' => $tender->id])->get();
            foreach ($medias as $media)
            {
                if (file_exists(storage_path('app/public/images/'.$media->media))) {
                    File::delete(storage_path('app/public/images/'.$media->media));
                }
            }
            $medias->each->delete();
        }
    }
}
