<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    protected $table = 'post_comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'post_comments',
        'created_at',
        'updated_at',
        'updated_by',
        'deleted_at',
        'delete_by',
    ];

    // A comment belongs to a post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // A comment belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
