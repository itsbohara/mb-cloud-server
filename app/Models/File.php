<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\Provider\Node\RandomNodeProvider;

class File extends Model
{
    //
    protected $fillable = [
        'name', 'path', 'extension', 'user_id', 'bucket_id',
    ];

    /**
     * Indicates if the IDs are UUID's.
     *
     * @var bool
     */
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $nodeProvider = new RandomNodeProvider();

            /* validate duplicate UUID */
            do {

                $uuid = Uuid::uuid1($nodeProvider->getNode());

                $uuid_exist = self::where('id', $uuid)->exists();

            } while ($uuid_exist);

            $model->id = $uuid;
        });
    }
}
