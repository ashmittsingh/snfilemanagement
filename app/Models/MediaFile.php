<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFile extends Model
{
    protected $table = 'media_files';

    protected $fillable = [
        'media_directory_id',
        'file_name',
        'file_path',
        'storage_path',
        'storage_url',
        'bunny_video_id',
        'bunny_play_url',
        'bunny_embed_url',
        'bunny_status',
    ];

    public function directory(): BelongsTo
    {
        return $this->belongsTo(
            MediaDirectory::class,
            'media_directory_id'
        );
    }
}