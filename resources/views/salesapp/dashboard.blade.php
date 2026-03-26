@extends('salesapp.layouts.main')
@section('main-section')
    <div class="row align-items-center mb-3">
        <div class="col-auto col-sm-auto mb-sm-0">
            <figure class="avatar avatar-50 coverimg rounded-circle"> <i class="fa fa-user-circle" style="font-size: 3rem"
                    aria-hidden="true"></i>
            </figure>
        </div>
        <div class="col col-sm">
            <p class="h2 mb-0">Hii, {{ $user->name }}</p>
            <p class="h5 text-secondary mb-0">{{ $user->vehicle_no }}</p>
        </div>
    </div>


    <div class="row gx-3">
        <div class="col-6 col-lg-6 col-xxl-3 mb-3">
            <a href="/sales-app/ready-to-deliver?status=pending" style="text-decoration: none">
                <div class="card adminuiux-card">
                    <div class="card-body">
                        <div class="row gx-2 gx-sm-3 align-items-center">
                            <div class="col">
                                <p class="h4 mb-0"><i class="bi bi-people fs-4 text-success-emphasis"></i>
                                    {{ $RFD }}
                                </p>
                                <p class="text-secondary small">Ready For Dispatch</p>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>



        <div class="col-6 col-lg-6 col-xxl-3 mb-3">
            <a href="/sales-app/ready-to-deliver?status=delivered" style="text-decoration: none">
                <div class="card adminuiux-card">
                    <div class="card-body">
                        <div class="row gx-2 gx-sm-3 align-items-center">
                            <div class="col">
                                <p class="h4 mb-0"><i class="bi bi-person-badge fs-4 text-info-emphasis"></i>
                                    {{ $delivered }}
                                </p>
                                <p class="text-secondary small">Delivered</p>
                            </div>

                        </div>
                    </div>
                </div>
            </a>
        </div>

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
                    data: @json($chartData),
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
