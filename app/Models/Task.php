<?php

namespace App\Models;

use Src\Facades\Model;

class Task extends Model
{
    protected string $table = 'tasks';
    protected bool $timestamps = true;
    protected string $updatedField = 'updated_at';
    protected string $createdField = 'created_at';

    protected array $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];
}
