<?php

namespace App\Observers;

use App\Models\AppMedia;
use App\Models\TenderOffer;
use Illuminate\Support\Facades\File;

class TenderOfferObserver
{
    public function saved(TenderOffer $tender_offer)
    {
        if (request()->hasFile('images')) {
            foreach (request()->file('images') as $image)
            {
                $image = $image->store('/tender_offers', 'uploads');
                $tender_offer->media()->create(['media' => $image, 'media_type' => 'image', 'option' => 'tender_offer_image']);
            }
        }

        if (request()->hasFile('files'))
        {
            foreach (request()->file('files') as $file)
            {
                $file = $file->store('/tender_offers', 'uploads');
                $tender_offer->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'tender_offer_file']);
            }
        }
    }

    public function deleted(TenderOffer $tender_offer)
    {
        if ($tender_offer->media()->exists()) {
            $medias = AppMedia::where(['app_mediaable_type' => 'App\Models\TenderOffer', 'app_mediaable_id' => $tender_offer->id])->get();
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
