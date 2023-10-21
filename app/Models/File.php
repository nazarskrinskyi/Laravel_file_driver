<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class File extends Model
{
    use HasFactory, HasCreatorAndUpdater, NodeTrait, SoftDeletes;

    protected $table = 'files';
    protected $guarded = false;

    public function isOwnedBy(int $user_id): bool
    {
        return $this->parent_id == $user_id;
    }
}
