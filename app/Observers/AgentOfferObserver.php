<?php

namespace App\Observers;

use App\Models\AgentOffer;
use App\Models\AppMedia;
use Illuminate\Support\Facades\File;

class AgentOfferObserver
{
    public function saved(AgentOffer $agent_offer)
    {
        if (request()->hasFile('images')) {
            foreach (request()->file('images') as $image)
            {
                $image = $image->store('/agent_offers', 'uploads');
                $agent_offer->media()->create(['media' => $image, 'media_type' => 'image', 'option' => 'agent_offer_image']);
            }
        }

        if (request()->hasFile('files'))
        {
            foreach (request()->file('files') as $file)
            {
                $file = $file->store('/agent_offers', 'uploads');
                $agent_offer->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'agent_offer_file']);
            }
        }
    }

    public function deleted(AgentOffer $agent_offer)
    {
        if ($agent_offer->media()->exists()) {
            $medias = AppMedia::where(['app_mediaable_type' => 'App\Models\Agent', 'app_mediaable_id' => $agent_offer->id])->get();
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
