<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\LocalUser;
use App\Models\Note;
use App\Models\ReadingProgress;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data untuk Kartu Statistik
        $totalUsers = LocalUser::count();
        $totalBooks = Book::count();
        // Mengubah 'Finished' menjadi 'FINISH' agar sesuai dengan database
        $booksFinished = UserLibrary::where('status', 'FINISH')->count();
        $totalNotes = Note::count();

        // 2. Data untuk Grafik Status Buku (Pie Chart)
        $statusCounts = UserLibrary::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // ======================= PERBAIKAN DI SINI =======================
        // Sesuaikan nama status dengan yang ada di database Anda
        $pieChartData = [
            'finished' => $statusCounts->get('FINISH', 0),
            'reading' => $statusCounts->get('READING', 0),
            'plan_to_read' => $statusCounts->get('PLAN_TO_READ', 0),
        ];
        // ================================================================

        // Cek jika total semua data pie chart adalah 0
        if (array_sum($pieChartData) == 0) {
            // Jika ya, berikan data contoh agar grafik tetap muncul
            $pieChartValues = [1, 1, 1];
        } else {
            // Jika tidak, gunakan data asli
            $pieChartValues = array_values($pieChartData);
        }

        // 3. Data untuk Grafik Progress Membaca (Area Chart)
        $progressData = ReadingProgress::where('recorded_at', '>=', Carbon::now()->subDays(7))
            ->select(DB::raw('DATE(recorded_at) as date'), DB::raw('SUM(page_read) as pages'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $areaChartLabels = [];
        $areaChartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('d M');
            $areaChartLabels[] = $date;
            $areaChartValues[$date] = 0;
        }
        foreach ($progressData as $progress) {
             $date = Carbon::parse($progress->date)->format('d M');
             if(isset($areaChartValues[$date])) {
                $areaChartValues[$date] = $progress->pages;
             }
        }

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalBooks' => $totalBooks,
            'booksFinished' => $booksFinished,
            'totalNotes' => $totalNotes,
            'pieChartData' => $pieChartValues, // Kirim data yang sudah diperbaiki
            'areaChartLabels' => array_keys($areaChartValues),
            'areaChartValues' => array_values($areaChartValues),
        ]);
    }
}
