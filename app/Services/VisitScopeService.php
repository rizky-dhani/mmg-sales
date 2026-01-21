<?php

namespace App\Services;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VisitScopeService
{
    /**
     * Get a scoped query for visits based on the user's hierarchy.
     */
    public function getVisitQuery(User $user): Builder
    {
        $query = Visit::query();

        if ($user->hasRole('Super Admin') || $user->hasRole('Board of Director') || $user->hasRole('Head')) {
            return $query;
        }

        // Get IDs of all subordinates recursively
        $subordinateIds = $this->getAllSubordinateIds($user);

        // Include the user's own ID
        $allowedUserIds = $subordinateIds->push($user->id);

        return $query->whereIn('user_id', $allowedUserIds);
    }

    /**
     * Get all subordinate IDs recursively for a given user.
     */
    public function getAllSubordinateIds(User $user): Collection
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('Board of Director') || $user->hasRole('Head')) {
            return User::pluck('id');
        }

        $subordinateIds = collect();

        foreach ($user->subordinates as $subordinate) {
            $subordinateIds->push($subordinate->id);
            $subordinateIds = $subordinateIds->merge($this->getAllSubordinateIds($subordinate));
        }

        return $subordinateIds->unique();
    }

    /**
     * Get visit statistics for a user's scope.
     */
    public function getVisitStats(User $user): array
    {
        $query = $this->getVisitQuery($user);

        $currentMonthVisits = (clone $query)
            ->whereYear('visit_started_at', now()->year)
            ->whereMonth('visit_started_at', now()->month)
            ->count();

        $lastMonthVisits = (clone $query)
            ->whereYear('visit_started_at', now()->subMonth()->year)
            ->whereMonth('visit_started_at', now()->subMonth()->month)
            ->count();

        $growth = 0;
        if ($lastMonthVisits > 0) {
            $growth = (($currentMonthVisits - $lastMonthVisits) / $lastMonthVisits) * 100;
        } elseif ($currentMonthVisits > 0) {
            $growth = 100;
        }

        // Top SR count with most visit to a customer
        $topRepCustomer = (clone $query)
            ->selectRaw('user_id, customer_id, count(*) as visit_count')
            ->groupBy('user_id', 'customer_id')
            ->orderByDesc('visit_count')
            ->first();

        return [
            'total' => (clone $query)->count(),
            'monthly' => $currentMonthVisits,
            'growth' => round($growth, 1),
            'top_rep_customer_count' => $topRepCustomer?->visit_count ?? 0,
            'top_rep_name' => $topRepCustomer?->user?->name ?? 'N/A',
        ];
    }

    /**
     * Get recent visits for a user's scope.
     */
    public function getRecentVisits(User $user, int $limit = 5): Collection
    {
        return $this->getVisitQuery($user)
            ->with(['customer', 'user'])
            ->latest('visit_started_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get query for leaderboard grouped by rep and customer.
     */
    public function getRepCustomerLeaderboardQuery(User $user): Builder
    {
        return Visit::query()
            ->whereIn('user_id', $this->getAllSubordinateIds($user)->push($user->id))
            ->selectRaw('user_id, customer_id, count(*) as visit_count')
            ->groupBy('user_id', 'customer_id')
            ->with(['user:id,name', 'customer:id,facility_name'])
            ->orderByDesc('visit_count');
    }

    /**
     * Get visit breakdown by sales representative for a user's scope.
     */
    public function getRepBreakdown(User $user): Collection
    {
        // For the leaderboard, we only care about subordinates if they have visits
        // If the user is an SR, they only see themselves

        $query = $this->getVisitQuery($user);

        return $query->selectRaw('user_id, count(*) as visit_count')
            ->groupBy('user_id')
            ->with('user:id,name')
            ->orderByDesc('visit_count')
            ->get();
    }

    /**
     * Get visit statistics for a specific customer within user's scope.
     */
    public function getCustomerVisitStats(User $user, int $customerId): array
    {
        $query = $this->getVisitQuery($user)->where('customer_id', $customerId);

        $total = (clone $query)->count();
        $lastVisit = (clone $query)->latest('visit_started_at')->first();

        return [
            'total' => $total,
            'last_visit_date' => $lastVisit?->visit_started_at,
        ];
    }
}
