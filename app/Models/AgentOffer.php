<?php

namespace App\Models;

use App\Observers\AgentOfferObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentOffer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    protected static function boot()
    {
        parent::boot();
        AgentOffer::observe(AgentOfferObserver::class) ;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAgentOfferImagesAttribute()
    {
        return $this->media()->where(['media_type' => 'image', 'option' => 'agent_offer_image'])->get();
    }

    public function getAgentOfferFilesAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'agent_offer_file'])->get();
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }
}
