<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyClient extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function setAvatarAttribute($value)
    {
        if ($value && $value->isValid()) {
            if (isset($this->attributes['avatar']) && $this->attributes['avatar']) {
                if (file_exists(storage_path('app/public/images/my_clients/' . $this->attributes['avatar']))) {
                    unlink(storage_path('app/public/images/my_clients/' . $this->attributes['avatar']));
                }
            }
            $image = $value->store('/my_clients', 'uploads');
            $this->attributes['avatar'] = $image;
        }
    }

    public function getAvatarAttribute()
    {
        $image = isset($this->attributes['avatar']) && $this->attributes['avatar'] ? 'storage/images/my_clients/' . $this->attributes['avatar'] : 'dashboardAssets/images/avatars/6.png';
        return asset($image);
    }
}
