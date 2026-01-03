<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk mendapatkan data rating
        $query = Session::with(['member', 'cs'])
            ->whereNotNull('rating_pelayanan');

        // Filter by CS
        if ($request->filled('cs_id')) {
            $query->where('cs_id', $request->cs_id);
        }

        // Filter by Rating
        if ($request->filled('rating')) {
            $query->where('rating_pelayanan', $request->rating);
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('rating_pelayanan_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('rating_pelayanan_at', '<=', $request->date_to);
        }

        // Order by rating date ascending (data baru di bawah)
        $ratings = $query->orderBy('rating_pelayanan_at', 'asc')->paginate(20);

        // Get CS list for filter
        $csList = User::where('role', 'cs')->get();

        // Calculate statistics
        $totalRatings = Session::whereNotNull('rating_pelayanan')->count();
        $averageRating = Session::whereNotNull('rating_pelayanan')->avg('rating_pelayanan');
        
        // Rating distribution
        $ratingDistribution = Session::whereNotNull('rating_pelayanan')
            ->select('rating_pelayanan', DB::raw('count(*) as count'))
            ->groupBy('rating_pelayanan')
            ->orderBy('rating_pelayanan', 'asc')
            ->get()
            ->pluck('count', 'rating_pelayanan')
            ->toArray();

        // CS Performance
        $csPerformance = User::where('role', 'cs')
            ->withCount(['sessions as total_ratings' => function($query) {
                $query->whereNotNull('rating_pelayanan');
            }])
            ->get()
            ->map(function($cs) {
                $avgRating = Session::where('cs_id', $cs->id)
                    ->whereNotNull('rating_pelayanan')
                    ->avg('rating_pelayanan');
                
                return [
                    'name' => $cs->name,
                    'avg_rating' => $avgRating ? round($avgRating, 2) : 0,
                    'total_ratings' => $cs->total_ratings
                ];
            })
            ->filter(function($cs) {
                return $cs['total_ratings'] > 0; // Only CS with ratings
            })
            ->sortByDesc('avg_rating')
            ->values();

        return view('admin.rating.index', compact(
            'ratings',
            'csList',
            'totalRatings',
            'averageRating',
            'ratingDistribution',
            'csPerformance'
        ));
    }
}
