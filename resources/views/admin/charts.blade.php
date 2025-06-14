@extends('layouts.admin')

@section('title', 'Charts')

@section('content')
    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">Charts</h1>

    {{-- Content Row --}}
    <div class="row">

        <div class="col-xl-8 col-lg-7">
            {{-- Area Chart --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Area Chart (Aktivitas Membaca 30 Hari Terakhir)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Bar Chart --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart (Buku Ditambahkan Tahun Ini)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="myBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Donut Chart --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Donut Chart (Status Buku)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-primary"></i> Selesai</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Sedang Dibaca</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Rencana Dibaca</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('plugins')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
@endpush

@push('scripts')
<script>
    window.onload = function() {
        // Global Defaults
        Chart.defaults.font.family = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.color = '#858796';

        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) { var k = Math.pow(10, prec); return '' + Math.round(n * k) / k; };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) { s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep); }
            if ((s[1] || '').length < prec) { s[1] = s[1] || ''; s[1] += new Array(prec - s[1].length + 1).join('0'); }
            return s.join(dec);
        }

        // Area Chart
        var ctxArea = document.getElementById("myAreaChart");
        if (ctxArea) {
            new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: @json($areaChartLabels),
                    datasets: [{
                        label: "Halaman Dibaca",
                        lineTension: 0.3,
                        backgroundColor: "rgba(85, 173, 155, 0.05)",
                        borderColor: "#55AD9B",
                        pointRadius: 3,
                        pointBackgroundColor: "#55AD9B",
                        pointBorderColor: "#55AD9B",
                        data: @json($areaChartValues),
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { maxTicksLimit: 7 }
                        },
                        y: {
                            ticks: {
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value) { return number_format(value); }
                            }
                        }
                    }
                }
            });
        }

        // Pie Chart
        var ctxPie = document.getElementById("myPieChart");
        if(ctxPie) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ["Selesai", "Sedang Dibaca", "Rencana Dibaca"],
                    datasets: [{
                        data: @json($pieChartData),
                        backgroundColor: ['#55AD9B', '#95D2B3', '#D8EFD3'],
                        hoverBackgroundColor: ['#357569', '#80c2a2', '#c0e0b8'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: { maintainAspectRatio: false, cutout: '80%', plugins: { legend: { display: false } } },
            });
        }

        // Bar Chart
        var ctxBar = document.getElementById("myBarChart");
        if(ctxBar) {
            new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: @json($barChartLabels),
                    datasets: [{
                        label: "Jumlah Buku",
                        backgroundColor: "#55AD9B",
                        hoverBackgroundColor: "#357569",
                        borderColor: "#55AD9B",
                        data: @json($barChartValues),
                    }],
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { ticks: { beginAtZero: true } } } }
            });
        }
    };
</script>
@endpush
