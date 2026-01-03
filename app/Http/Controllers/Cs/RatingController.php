<?php

namespace App\Http\Controllers\Cs;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index(Request $request)
    {
        $cs = Auth::guard('cs')->user();

        $query = Session::with(['member'])
            ->where('cs_id', $cs->id)
            ->whereNotNull('rating_pelayanan');

        $ratings = (clone $query)
            ->orderBy('rating_pelayanan_at', 'desc')
            ->paginate(20);

        $totalRatings = (clone $query)->count();
        $averageRating = (clone $query)->avg('rating_pelayanan');

        return view('cs.rating.index', compact('ratings', 'totalRatings', 'averageRating'));
    }
}
