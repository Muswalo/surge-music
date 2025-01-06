<?php

namespace Muswalo\Surgemusic\Models\Music\Core;

use Muswalo\Surgemusic\Models\Base\Model;

/**
 * Class Song
 * @property string $song_title
 * @property string $artist_name
 * @property string $album_name
 * @property string $genre
 * @property string $release_date
 * @property string $song
 * @property string $cover_art
 * @property string $description
 * @property string $duration
 * @property int $user_id
 * @property string $upload_date
 * 
 */
class Song extends Model
{
    protected string $table = 'songs';
    protected array $fillable = [
        'song_title',
        'artist_name',
        'album_name',
        'genre',
        'release_date',
        'song',
        'cover_art',
        'description',
        'duration',
        'user_id',
        'upload_date',
        'isrc_code',
        'published',
        'tag_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getById(int $id): array
    {
        return ['data' => $this->attributes = $this->find($id)->attributes];
    }
}
