<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'title',
        'file_path',
        'file_type',
        'created_at',
        'updated_at',
        'updated_by',
        'deleted_at',
        'delete_by',
    ];

    // Comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Comment belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
