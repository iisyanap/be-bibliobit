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
        $request->validate(['filter' => 'sometimes|in:day,week,month,year']);
        $filter = $request->query('filter', 'week');
        $userId = $request->user->uid;

        $now = Carbon::now();
        $startDate = match ($filter) {
            'day'   => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(Carbon::MONDAY),
            'month' => $now->copy()->startOfMonth(),
            'year'  => $now->copy()->startOfYear(),
        };
        $endDate = $now->copy()->endOfDay();

        // Untuk filter tahun, kita ambil 4 tahun terakhir
        if ($filter === 'year') {
            $startDate = $now->copy()->subYears(3)->startOfYear();
        }

        // Ambil data mentah dari database
        $pagesReadRaw = $this->getRawData($userId, 'reading_progress', 'recorded_at', 'SUM(page_read)', $startDate, $endDate, $filter);
        $booksFinishedRaw = $this->getRawData($userId, 'user_library', 'updated_at', 'COUNT(*)', $startDate, $endDate, $filter, 'FINISH');

        // Proses data mentah menjadi data statistik yang lengkap
        $pagesReadData = $this->generateCompleteData($filter, $pagesReadRaw);
        $booksFinishedData = $this->generateCompleteData($filter, $booksFinishedRaw);

        // Hitung total dari data yang sudah difilter
        $totalPagesRead = ReadingProgress::where('user_id', $userId)->whereBetween('recorded_at', [$startDate, $endDate])->sum('page_read');
        $totalBooksFinished = UserLibrary::where('user_id', $userId)->where('status', 'FINISH')->whereBetween('updated_at', [$startDate, $endDate])->count();

        // --- DIPERBAIKI: Reading history sekarang juga difilter ---
        $readingHistory = ReadingProgress::with(['userLibrary.book'])
            ->where('user_id', $userId)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'desc')
            ->limit(50) // Batasi untuk performa
            ->get();

        $finishedBooks = UserLibrary::with('book')
            ->where('user_id', $userId)
            ->where('status', 'FINISH')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->limit(50)
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

    private function getRawData($userId, $table, $dateColumn, $aggregate, $startDate, $endDate, $filter, $status = null) {
        $labelQuery = match ($filter) {
            'day'   => "EXTRACT(HOUR FROM {$dateColumn})",
            'week'  => "EXTRACT(ISODOW FROM {$dateColumn})",
            'month' => "EXTRACT(MONTH FROM {$dateColumn})",
            'year'  => "EXTRACT(YEAR FROM {$dateColumn})",
        };
        $query = DB::table($table)->select(DB::raw("{$aggregate} as value, {$labelQuery} as label"))->where('user_id', $userId)->whereBetween($dateColumn, [$startDate, $endDate])->groupBy('label');
        if ($status) { $query->where('status', $status); }
        return $query->pluck('value', 'label');
    }

    private function generateCompleteData($filter, $rawData) {
        $completeData = []; $now = Carbon::now();
        switch ($filter) {
            case 'day':
                for ($hour = 0; $hour < 24; $hour++) { $completeData[sprintf('%02d:00', $hour)] = 0; }
                foreach ($rawData as $hour => $value) { $completeData[sprintf('%02d:00', $hour)] = (int)$value; }
                break;
            case 'week':
                $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                $dayMap = array_fill_keys($days, 0);
                foreach ($rawData as $dayOfWeek => $value) { if ($dayOfWeek >= 1 && $dayOfWeek <= 7) { $dayMap[$days[$dayOfWeek-1]] = (int)$value; } }
                $completeData = $dayMap;
                break;
            case 'month':
                // --- DIPERBAIKI: Menggunakan format 'M' untuk singkatan bulan ---
                for ($month = 1; $month <= 12; $month++) { $completeData[Carbon::create()->month($month)->format('M')] = 0; }
                foreach ($rawData as $month => $value) { $completeData[Carbon::create()->month($month)->format('M')] = (int)$value; }
                break;
            case 'year':
                for ($i = 3; $i >= 0; $i--) { $completeData[$now->copy()->subYears($i)->year] = 0; }
                foreach ($rawData as $year => $value) { if (isset($completeData[$year])) { $completeData[$year] = (int)$value; } }
                ksort($completeData); break;
        }
        return $completeData;
    }
}
