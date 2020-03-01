<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Image\Manipulations;

class Project extends Model implements HasMedia
{
    use HasMediaTrait;
    protected $guarded = ['id'];


    public function registerMediaConversions(?\Spatie\MediaLibrary\Models\Media $media = null)
    {

        $this->addMediaConversion('thumb')
            ->width(368)
            ->height(232)
            ->sharpen(10)
            ->performOnCollections('test2');

        $this->addMediaConversion('medium')
            ->width(1200)
            ->performOnCollections('test2');
    }


    public function path()
    {
        return "/projects/{$this->id}";
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
