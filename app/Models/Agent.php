<?php

namespace App\Models;

use App\Observers\AgentObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates    = ['expiry_date'];

    protected static function boot()
    {
        parent::boot();
        Agent::observe(AgentObserver::class) ;
    }

    public function setUserIdAttribute($value)
    {
        if ($value)
        {
            $this->attributes['user_id'] = $value;
        }
        else
        {

            $this->attributes['user_id'] = auth('api')->id();
        }
    }

    public function getAgentImagesAttribute()
    {
        return $this->media()->where(['media_type' => 'image', 'option' => 'agent_image'])->get();
    }

    public function getAgentFilesAttribute()
    {
        return $this->media()->where(['media_type' => 'file', 'option' => 'agent_file'])->get();
    }

    public function media()
    {
        return $this->morphOne(AppMedia::class, 'app_mediaable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function offers()
    {
        return $this->hasMany(AgentOffer::class);
    }

    public function usersfavorite()
    {
        return $this->belongsToMany(User::class, 'favorite_agents');
    }
}
