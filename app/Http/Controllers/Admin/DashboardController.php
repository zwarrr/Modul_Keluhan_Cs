<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use App\Models\Member;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total komplain hari ini
        $complainsToday = Session::whereDate('created_at', Carbon::today())->count();
        
        // 2. Total komplain bulan ini
        $complainsThisMonth = Session::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // 3. Komplain berdasarkan status
        $complainsOpen = Session::where('status', 'open')->count();
        $complainsPending = Session::where('status', 'pending')->count();
        $complainsClosed = Session::where('status', 'closed')->count();
        
        // 4. Total member aktif
        $totalMembers = Member::count();
        
        // 5. Total CS aktif
        $totalCS = User::where('role', 'cs')->count();
        
        // 6. Komplain terbaru (10 terakhir)
        $recentComplains = Session::with(['member', 'cs'])
            ->orderBy('last_activity', 'desc')
            ->limit(10)
            ->get();
        
        // 7. Top 5 CS berdasarkan komplain yang diselesaikan
        $topCS = User::where('role', 'cs')
            ->withCount(['sessions as closed_count' => function($query) {
                $query->where('status', 'closed');
            }])
            ->orderBy('closed_count', 'desc')
            ->limit(5)
            ->get();
        
        // 8. Daftar CS dengan komplain aktif
        $csList = User::where('role', 'cs')
            ->withCount(['sessions as active_count' => function($query) {
                $query->whereIn('status', ['open', 'pending']);
            }])
            ->with(['sessions' => function($query) {
                $query->select('cs_id', DB::raw('AVG(rating_pelayanan) as avg_rating'))
                    ->whereNotNull('rating_pelayanan')
                    ->groupBy('cs_id');
            }])
            ->get()
            ->map(function($cs) {
                // Calculate average rating manually
                $avgRating = Session::where('cs_id', $cs->id)
                    ->whereNotNull('rating_pelayanan')
                    ->avg('rating_pelayanan');
                
                $cs->avg_rating = $avgRating ? round($avgRating, 1) : '-';
                return $cs;
            });
        
        // 9. Data untuk grafik - Komplain 7 hari terakhir
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Session::whereDate('created_at', $date)->count();
            $last7Days->push([
                'date' => $date->format('d/m'),
                'count' => $count
            ]);
        }
        
        // 10. Data untuk grafik - Komplain per bulan (6 bulan terakhir)
        $last6Months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = Session::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $last6Months->push([
                'month' => $date->format('M Y'),
                'count' => $count
            ]);
        }
        
        // 11. Data untuk grafik - Rating CS
        $ratingData = User::where('role', 'cs')
            ->get()
            ->map(function($cs) {
                $avgRating = Session::where('cs_id', $cs->id)
                    ->whereNotNull('rating_pelayanan')
                    ->avg('rating_pelayanan');
                return [
                    'name' => $cs->name,
                    'rating' => $avgRating ? round($avgRating, 1) : 0
                ];
            })
            ->sortByDesc('rating')
            ->take(10)
            ->values();
        
        return view('admin.index', compact(
            'complainsToday',
            'complainsThisMonth',
            'complainsOpen',
            'complainsPending',
            'complainsClosed',
            'totalMembers',
            'totalCS',
            'recentComplains',
            'topCS',
            'csList',
            'last7Days',
            'last6Months',
            'ratingData'
        ));
    }
}
