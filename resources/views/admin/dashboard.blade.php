@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row - Kartu Statistik -->
    <div class="row">
        <!-- Total Pengguna Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Pengguna</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Buku Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Buku</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buku Selesai Dibaca Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Buku Selesai Dibaca
                            </div>
                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $booksFinished }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Catatan Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Catatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalNotes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sticky-note fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Content Row - Charts -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Membaca (Halaman per Hari)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pie Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Buku Pengguna</h6>
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
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "#357569",
                        pointHoverBorderColor: "#357569",
                        hitRadius: 10,
                        borderWidth: 2,
                        data: @json($areaChartValues),
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            titleColor: '#6e707e',
                            titleFont: { size: 14 },
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            padding: 15,
                            displayColors: false,
                            intersect: false,
                            mode: 'index',
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) { label += ': '; }
                                    if (context.parsed.y !== null) { label += number_format(context.parsed.y); }
                                    return label;
                                }
                            }
                        }
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
                            },
                            grid: {
                                color: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
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
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            padding: 15,
                            displayColors: false,
                        },
                    },
                    cutout: '80%',
                },
            });
        }
    };
</script>
@endpush
