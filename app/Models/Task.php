<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \LaravelArchivable\Archivable;

use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Tags\HasTags;

class Task extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, Archivable, HasTags;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'priority',
        'order',
        'completed_at',
        'created_by',
        'user_id',
    ];

    protected $dates = [
        'completed_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'due_date',
    ];

    protected $hidden = [
        'user_id',
        'deleted_at',
    ];

    protected $casts = [
        'due_date' => 'datetime:Y-m-d',
    ];
    protected $appends = ['task_order', 'completed', 'archived'];

    public function getTaskOrderAttribute()
    {
        return $this->id + 1;  
    }
    public function getCompletedAttribute()
    {
        return !empty($this->completed_at);
    }
    public function getArchivedAttribute()
    {
        return !empty($this->archived_at); 
    }

}
