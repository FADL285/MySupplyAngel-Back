<?php

namespace App\Observers;

use App\Models\Agent;
use App\Models\AppMedia;
use Illuminate\Support\Facades\File;

class AgentObserver
{
    public function saved(Agent $agent)
    {
        if (request()->hasFile('agent_images'))
        {
            foreach (request()->file('agent_images') as $image)
            {
                $img = $image->store('/agents', 'uploads');
                $agent->media()->create(['media' => $img,'media_type' => 'image', 'option' => 'agent_image']);
            }
        }

        if (request()->hasFile('agent_files'))
        {
            foreach (request()->file('agent_files') as $file)
            {
                $file = $file->store('/agents', 'uploads');
                $agent->media()->create(['media' => $file,'media_type' => 'file', 'option' => 'agent_file']);
            }
        }
    }

    public function deleted(Agent $agent)
    {
        if ($agent->media()->exists()) {
            $medias = AppMedia::where(['app_mediaable_type' => 'App\Models\Agent', 'app_mediaable_id' => $agent->id])->get();
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
