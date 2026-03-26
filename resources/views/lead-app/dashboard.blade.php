@extends('lead-app.layouts.main')
@section('main-section')
    <style>
        @media (max-width: 768px) {

            /* Dashboard grid */
            .mobile-dashboard {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            /* Card */
            .mobile-dashboard .adminuiux-card {
                border-radius: 18px;
                border: none;
                box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
                transition: transform .2s ease, box-shadow .2s ease;
            }

            .mobile-dashboard .adminuiux-card:active {
                transform: scale(.97);
                box-shadow: 0 6px 14px rgba(0, 0, 0, .15);
            }

            /* Card body */
            .mobile-dashboard .card-body {
                padding: 16px;
            }

            /* Numbers */
            .mobile-dashboard .h4 {
                font-size: 26px;
                font-weight: 800;
                margin-bottom: 4px;
            }

            /* Label */
            .mobile-dashboard .small {
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .3px;
            }

            /* Icon */
            .mobile-dashboard i {
                font-size: 18px;
                opacity: .9;
            }

            /* Total leads highlight */
            .total-card {
                background: linear-gradient(135deg, #4f46e5, #3b82f6);
                color: #fff;
            }

            .total-card .text-secondary,
            .total-card i {
                color: #e0e7ff !important;
            }

            /* Status accent bar */
            .status-card::before {
                content: '';
                display: block;
                height: 5px;
                border-radius: 18px 18px 0 0;
                background: var(--status-color, #0d6efd);
            }

        }
    </style>

    <div class="row align-items-center mb-3">
        <div class="col-auto col-sm-auto mb-sm-0">
            <figure class="avatar avatar-50 coverimg rounded-circle"> <i class="fa fa-user-circle" style="font-size: 3rem"
                    aria-hidden="true"></i>
            </figure>
        </div>
        <div class="col col-sm">
            <p class="h2 mb-0">Hii, {{ $user->name }}</p>
            <p class="h5 text-secondary mb-0">{{ $user->email }}</p>
        </div>
    </div>


    <div class="row gx-3 mobile-dashboard">
        <div class="col">
            <a href="#" style="text-decoration:none">
                <div class="card adminuiux-card total-card">
                    <div class="card-body">
                        <p class="h4">
                            <i class="fa fa-chart-line me-1"></i>
                            {{ $totalLeads }}
                        </p>
                        <p class="small mb-0">Total Leads</p>
                    </div>
                </div>
            </a>
        </div>

        @foreach ($leadStatusCount as $item)
            @php
                $colors = [
                    1 => '#DCFCE7', // soft mint green (New / Fresh)
                    2 => '#FEF3C7', // warm pastel amber (Follow-up)
                    3 => '#DBEAFE', // soft sky blue (In Progress / Closed)
                    4 => '#FEE2E2', // light coral red (Lost / Cancelled)
                    5 => '#EDE9FE', // soft lavender (Special / Hold)
                    6 => '#FDE8FF', // soft lavender (Special / Hold)
                    7 => 'lightblue', // soft lavender (Special / Hold)
                ];

                $color = $colors[$item->id] ?? '#6366f1';
            @endphp

            <div class="col">
                <a href="/lead-app/leads?status={{ $item->id }}" style="text-decoration:none">
                    <div class="card adminuiux-card  " style="background-color: {{ $color }}">
                        <div class="card-body">
                            <p class="h4">
                                {{ $item->lead_count }}
                            </p>
                            <p class="small text-secondary mb-0">
                                {{ $item->status }}
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach



    </div>
    <hr>
    <div class="row gx-3">

        <div class="col-12">
            <canvas id="leadsChart"></canvas>
        </div>



    </div>








    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('leadsChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Delivered Orders',
                    data: @json($monthlyLeads),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Monthly Delivered Orders'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
