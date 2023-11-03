<?php

namespace App\Models;

use App\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kalnoy\Nestedset\NodeTrait;

class File extends Model
{
    use HasFactory, HasCreatorAndUpdater, NodeTrait, SoftDeletes;

    protected $table = 'files';
    protected $guarded = false;


    public function isOwnedBy($userId): bool
    {
        return $this->created_by == $userId;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner(): Attribute
    {
        return Attribute::make(get: function () {
            return $this->attributes['created_by'] === Auth::id() ? 'me' : $this->user->name;
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(File::class, 'parent_id');
    }

    public function get_file_size(): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        $power = $this->size > 0 ? floor(log($this->size, 1024)) : 0;

        return number_format($this->size / pow(1024, $power), 2, '.', ',') . $units[$power];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->parent) return;

            $model->path = (!$model->parent->isRoot() ? $model->parent->path . '/' : '') . Str::slug($model->name);
        });

        static::deleted(function (File $model) {
            if (!$model->is_folder) {
                Storage::delete($model->storage_path);
            } else {

            }
        });
    }

    private function deleteFolder(File $model): void
    {
        $children = File::all()->where('parent_id', $model->id);
        foreach ($children as $child) {
            if (!$child->is_folder) {
                Storage::delete($child->storage_path);
            }
            else {
                $this->deleteFolder($child);
            }
        }
    }

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }
}
