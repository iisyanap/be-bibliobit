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

        // Tentukan rentang tanggal berdasarkan filter
        $startDate = match ($filter) {
            'day'   => $now->copy()->startOfDay(),
            'week'  => $now->copy()->startOfWeek(Carbon::MONDAY),
            'month' => $now->copy()->startOfMonth(),
            'year'  => $now->copy()->startOfYear(),
        };
        $endDate = $now->copy()->endOfDay();

        if ($filter === 'year') {
            $startDate = $now->copy()->subYears(3)->startOfYear();
        }

        // --- PERBAIKAN UTAMA: Menghitung selisih progress ---
        // 1. Ambil semua progress yang relevan untuk perhitungan delta
        $allProgressForUser = ReadingProgress::where('user_id', $userId)
            ->orderBy('user_library_id')
            ->orderBy('recorded_at')
            ->get();

        // 2. Hitung selisihnya di PHP
        $progressEvents = $this->calculateProgressDeltas($allProgressForUser);

        // 3. Filter event tersebut berdasarkan rentang tanggal
        $filteredEvents = collect($progressEvents)->filter(function ($event) use ($startDate, $endDate) {
            return Carbon::parse($event['recorded_at'])->between($startDate, $endDate);
        });

        // 4. Proses data yang sudah benar menjadi data statistik
        $pagesReadRaw = $this->groupEventsByFilter($filteredEvents, $filter);
        $pagesReadData = $this->generateCompleteData($filter, $pagesReadRaw);
        $totalPagesRead = $filteredEvents->sum('pages_read');

        // --- Logika untuk Buku Selesai (tetap sama dan sudah benar) ---
        $booksFinishedRaw = $this->getBooksFinishedRaw($userId, $startDate, $endDate, $filter);
        $booksFinishedData = $this->generateCompleteData($filter, $booksFinishedRaw);
        $totalBooksFinished = UserLibrary::where('user_id', $userId)->where('status', 'FINISH')->whereBetween('updated_at', [$startDate, $endDate])->count();

        $readingHistory = $filteredEvents->sortByDesc('recorded_at')->map(function($event) {
            // Perlu cara untuk menyatukan data buku, untuk sementara kita kirim datanya
            return [
                'user_library_id' => $event['user_library_id'],
                'page_read' => $event['pages_read'],
                'recorded_at' => $event['recorded_at'],
                'user_library' => UserLibrary::with('book')->find($event['user_library_id'])
            ];
        })->take(50)->values();

        $finishedBooks = UserLibrary::with('book')->where('user_id', $userId)->where('status', 'FINISH')->whereBetween('updated_at', [$startDate, $endDate])->orderBy('updated_at', 'desc')->limit(50)->get()->pluck('book');

        return response()->json([
            'totalPagesRead' => (int) $totalPagesRead,
            'pagesReadData' => $pagesReadData,
            'totalBooksFinished' => $totalBooksFinished,
            'booksFinishedData' => $booksFinishedData,
            'readingHistory' => $readingHistory,
            'finishedBooks' => $finishedBooks,
        ]);
    }

    /**
     * Fungsi helper baru untuk menghitung selisih progress.
     */
    private function calculateProgressDeltas($allProgress)
    {
        $events = [];
        $lastPageByBook = [];

        foreach ($allProgress as $progress) {
            $bookId = $progress->user_library_id;
            $previousPage = $lastPageByBook[$bookId] ?? 0;
            $pagesThisSession = $progress->page_read - $previousPage;

            if ($pagesThisSession > 0) {
                $events[] = [
                    'user_library_id' => $bookId,
                    'pages_read' => $pagesThisSession,
                    'recorded_at' => $progress->recorded_at,
                ];
            }
            $lastPageByBook[$bookId] = $progress->page_read;
        }
        return $events;
    }

    /**
     * Fungsi helper baru untuk mengelompokkan data event.
     */
    private function groupEventsByFilter($events, $filter)
    {
        return $events->groupBy(function ($event) use ($filter) {
            $date = Carbon::parse($event['recorded_at']);
            return match ($filter) {
                'day'   => $date->format('H'),
                'week'  => $date->dayOfWeekIso,
                'month' => $date->format('n'), // 1-12
                'year'  => $date->year,
            };
        })->map(function ($group) {
            return $group->sum('pages_read');
        });
    }

    // Fungsi ini tidak berubah
    private function getBooksFinishedRaw($userId, $startDate, $endDate, $filter) {
        $labelQuery = match ($filter) {
            'day'   => "EXTRACT(HOUR FROM updated_at)", 'week'  => "EXTRACT(ISODOW FROM updated_at)",
            'month' => "EXTRACT(MONTH FROM updated_at)", 'year'  => "EXTRACT(YEAR FROM updated_at)",
        };
        return DB::table('user_library')->select(DB::raw("COUNT(*) as value, {$labelQuery} as label"))->where('user_id', $userId)->where('status', 'FINISH')->whereBetween('updated_at', [$startDate, $endDate])->groupBy('label')->pluck('value', 'label');
    }

    // Fungsi ini tidak berubah
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
