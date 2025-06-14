<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\UserLibrary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    /**
     * Menyiapkan data dan menampilkan halaman chart.
     */
    public function index()
    {
        // 1. Data untuk Pie Chart (Status Buku Pengguna) - Tidak ada perubahan
        $statusCounts = UserLibrary::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $pieChartData = [
            'finished' => $statusCounts->get('FINISH', 0),
            'reading' => $statusCounts->get('READING', 0),
            'plan_to_read' => $statusCounts->get('PLAN_TO_READ', 0),
        ];

        if (array_sum($pieChartData) == 0) {
            $pieChartValues = [1, 1, 1];
        } else {
            $pieChartValues = array_values($pieChartData);
        }

        // 2. Data untuk Bar Chart (Buku Ditambahkan per Bulan) - Tidak ada perubahan
        $barChartData = Book::select(
                DB::raw("COUNT(*) as count"),
                DB::raw("TO_CHAR(created_at, 'Mon') as month_name")
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month_name', DB::raw("EXTRACT(MONTH FROM created_at)"))
            ->orderBy(DB::raw("EXTRACT(MONTH FROM created_at)"), 'asc')
            ->pluck('count', 'month_name');

        // ======================= PERUBAHAN DATA AREA CHART =======================
        // 3. Data untuk Area Chart (Aktivitas Membaca 30 Hari Terakhir)
        $progressData = ReadingProgress::where('recorded_at', '>=', Carbon::now()->subDays(30))
            ->select(DB::raw('DATE(recorded_at) as date'), DB::raw('SUM(page_read) as pages'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(function($item) {
                // Format tanggal agar mudah dicocokkan
                return Carbon::parse($item->date)->format('d M');
            });

        $areaChartLabels = [];
        $areaChartValues = [];
        // Inisialisasi 30 hari terakhir dengan nilai 0
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('d M');
            $areaChartLabels[] = $date;
            // Ambil data dari query, atau gunakan 0 jika tidak ada
            $areaChartValues[] = $progressData->get($date)->pages ?? 0;
        }
        // =========================================================================

        return view('admin.charts', [
            'pieChartData' => $pieChartValues,
            'barChartLabels' => $barChartData->keys(),
            'barChartValues' => $barChartData->values(),
            'areaChartLabels' => $areaChartLabels,
            'areaChartValues' => $areaChartValues,
        ]);
    }
}
