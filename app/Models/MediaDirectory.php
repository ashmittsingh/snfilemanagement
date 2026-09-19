<?php

namespace App\Models;
use App\Models\MediaFile;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaDirectory extends Model
{
    protected $table = 'media_directories';

    protected $fillable = [
        'name',
        'description',
        'type',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'media_directory_id');
    }
}