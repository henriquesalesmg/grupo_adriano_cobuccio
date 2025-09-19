<?php

namespace App\Services;

use App\Models\Activity;

class ActivitiesService
{
    public function getActivities($userId, $filters = [])
    {
        $query = Activity::where('user_id', $userId);

        if (!empty($filters['data_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['data_inicio']);
        }
        if (!empty($filters['data_fim'])) {
            $query->whereDate('created_at', '<=', $filters['data_fim']);
        }

        return $query->orderByDesc('created_at')->get();
    }
}
