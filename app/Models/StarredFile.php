<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static insert(array $data)
 */
class StarredFile extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected $table = 'starred_files';
    protected $guarded = false;
}
