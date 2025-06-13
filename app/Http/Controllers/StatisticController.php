<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ReadingProgress;
use App\Models\UserLibrary;
use Carbon\Carbon;

class StatisticController extends Controller
{
    
    public function index(Request $request)
    {
        $request->validate([
            'filter' => 'sometimes|in:day,week,month,year',
        ]);

        $filter = $request->query('filter', 'week');
        $userId = $request->user->uid;

        $now = Carbon::now();
        $startDate = match ($filter) {
            'day'   => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            'year'  => $now->copy()->startOfYear(),
        };
        $endDate = $now->copy()->endOfDay();

        // menghitung halaman baca
        $pagesReadQuery = ReadingProgress::where('user_id', $userId)
            ->whereBetween('recorded_at', [$startDate, $endDate]);

        $totalPagesRead = $pagesReadQuery->sum('page_read');
        $pagesReadData = $this->groupDataByInterval($pagesReadQuery->clone(), $filter, 'recorded_at', 'SUM(page_read)');

        // menghitung buku selesai
        $booksFinishedQuery = UserLibrary::where('user_id', $userId)
            ->where('status', 'FINISH')
            ->whereBetween('updated_at', [$startDate, $endDate]);

        $totalBooksFinished = $booksFinishedQuery->count();
        $booksFinishedData = $this->groupDataByInterval($booksFinishedQuery->clone(), $filter, 'updated_at', 'COUNT(*)');

        // get progress reading
        $readingHistory = ReadingProgress::with(['userLibrary.book'])
            ->where('user_id', $userId)
            ->orderBy('recorded_at', 'desc')
            ->limit(20)
            ->get();

        // get finish books
        $finishedBooks = UserLibrary::with('book')
            ->where('user_id', $userId)
            ->where('status', 'FINISH')
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get()->pluck('book');

        return response()->json([
            'totalPagesRead' => (int) $totalPagesRead,
            'pagesReadData' => $pagesReadData,
            'totalBooksFinished' => $totalBooksFinished,
            'booksFinishedData' => $booksFinishedData,
            'readingHistory' => $readingHistory,
            'finishedBooks' => $finishedBooks,
        ]);
    }

    private function groupDataByInterval($query, $filter, $dateColumn, $aggregateFunction)
    {
        $labelFormat = match ($filter) {
            'day'   => "EXTRACT(HOUR FROM {$dateColumn})",
            'week'  => "TRIM(TO_CHAR({$dateColumn}, 'Day'))",
            'month' => "EXTRACT(DAY FROM {$dateColumn})",
            'year'  => "TRIM(TO_CHAR({$dateColumn}, 'Month'))",
        };

        return $query->select(DB::raw("{$aggregateFunction} as value, {$labelFormat} as label"))
                     ->groupBy('label')
                     ->pluck('value', 'label');
    }
}
