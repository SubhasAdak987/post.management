<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;
use App\Models\PostComment;

class StatisticsService
{
    public function getOverview()
    {
        return [
            'users' => User::count(),
            'posts' => Post::count(),
            'comments' => PostComment::count(),
        ];
    }

    public function getMonthlyPostData()
    {
        return Post::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }
}