<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">Admin Dashboard</div>

    <div class="dashboard">
        <div class="top-nav">
            <h1>Administrator</h1>
            <div class="nav-links">
                <a href="{{ route('admin.suppliers.index') }}">Suppliers</a>
                <a href="#">Order Track</a>
                <a href="{{ route('admin.employee.index') }}">Employee Assignment</a>
                <a href="{{ route('admin.sales-reports.index') }}">Sales Report</a>
                <a href="{{ route('admin.inventory.index') }}">Inventory</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="dashboard-content">
            <div class="track-label">Performance Metrics</div>
            
            <!-- Filter Interface -->
            <div class="filter-container">
                <label for="timePeriod">Select Time Period:</label>
                <select id="timePeriod" name="timePeriod">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
                <!-- Date picker for specific date/week/month/year -->
                <input type="date" id="dateFilter" style="display: none;">
                <input type="week" id="weekFilter" style="display: none;">
                <input type="month" id="monthFilter">
                <input type="number" id="yearFilter" min="2000" max="2099" style="display: none;">
                <button id="applyFilter" class="btn">Apply Filter</button>
            </div>

            <div class="summary mb-4">
                <h4>Service Totals</h4>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>Wash</h5>
                                <p id="washTotal">${{ number_format($totals['Wash'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>Fold</h5>
                                <p id="foldTotal">${{ number_format($totals['Fold'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>Ironing</h5>
                                <p id="ironingTotal">${{ number_format($totals['Ironing'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5>Total</h5>
                                <p id="allTotal">${{ number_format($totals['All'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="charts-container">
                <div class="chart-container">
                    <h3 class="chart-title">Sales Distribution</h3>
                    <canvas id="pieChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3 class="chart-title">Sales Trends</h3>
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(137.15deg, rgba(23, 232, 255, 0.00) 0%, rgba(23, 232, 255, 0.18) 86%, rgba(23, 232, 255, 0.20) 100%), 
                        linear-gradient(to left, rgba(7, 156, 214, 0.20), rgba(7, 156, 214, 0.20)), 
                        linear-gradient(119.69deg, rgba(93, 141, 230, 0.00) 0%, rgba(142, 176, 239, 0.10) 45.69%, rgba(36, 89, 188, 0.20) 96.88%), 
                        linear-gradient(to left, rgba(47, 53, 109, 0.20), rgba(47, 53, 109, 0.20));
            color: white;
            min-height: 100vh;
        }
        
        .header {
            background-color: rgba(26, 26, 26, 0.8);
            padding: 10px 20px;
            color: #9e9e9e;
            font-size: 14px;
            backdrop-filter: blur(5px);
        }
        
        .dashboard {
            background-color: rgba(28, 56, 86, 0.8);
            margin: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(5px);
        }
        
        .top-nav {
            display: flex;
            background-color: rgba(26, 45, 69, 0.8);
            padding: 15px 20px;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .top-nav h1 {
            flex: 1;
            margin: 0;
            font-size: 24px;
            font-weight: normal;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s;
            padding: 8px 12px;
            border-radius: 5px;
        }
        
        .nav-links a:hover {
            color: #17e8ff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .dashboard-content {
            padding: 20px;
        }
        
        .track-label {
            font-size: 18px;
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .chart-container {
            flex: 1;
            min-width: 300px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        
        .chart-title {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .logout-btn {
            background-color: rgba(255, 45, 32, 0.2);
            border: 1px solid rgba(255, 45, 32, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: rgba(255, 45, 32, 0.3);
        }

        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .filter-container select, .filter-container input, .filter-container button {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .filter-container button {
            background-color: rgba(23, 232, 255, 0.2);
            border: 1px solid rgba(23, 232, 255, 0.3);
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-container button:hover {
            background-color: rgba(23, 232, 255, 0.3);
        }

        .summary .card {
            margin-bottom: 20px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        .summary .card-body h5 {
            font-size: 1rem;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.9);
        }

        .summary .card-body p {
            font-size: 1.2rem;
            color: #17e8ff;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .col-md-3 {
            flex: 1;
            min-width: 200px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize charts
            let pieChart, lineChart;
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const lineCtx = document.getElementById('lineChart').getContext('2d');

            // Initial sales data from sales report
            const initialTotals = {
                Wash: {{ $totals['Wash'] }},
                Fold: {{ $totals['Fold'] }},
                Ironing: {{ $totals['Ironing'] }},
                All: {{ $totals['All'] }}
            };

            const orders = [
                @foreach($orders as $order)
                {
                    id: {{ $order->id }},
                    order_name: '{{ $order->order_name }}',
                    date: '{{ $order->date }}',
                    service_type: @json((array)$order->service_type),
                    status: '{{ $order->status }}',
                    amount: {{ $order->amount }}
                },
                @endforeach
            ];

            // Function to update charts and summary
            function updateDashboard(totals, filteredOrders, period, filterValue) {
                // Update summary cards
                document.getElementById('washTotal').textContent = `$${totals.Wash.toFixed(2)}`;
                document.getElementById('foldTotal').textContent = `$${totals.Fold.toFixed(2)}`;
                document.getElementById('ironingTotal').textContent = `$${totals.Ironing.toFixed(2)}`;
                document.getElementById('allTotal').textContent = `$${totals.All.toFixed(2)}`;

                // Update pie chart (service distribution)
                if (pieChartIrc) {
                    pieChart.destroy();
                }
                pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: ['Wash', 'Fold', 'Ironing'],
                        datasets: [{
                            data: [totals.Wash, totals.Fold, totals.Ironing],
                            backgroundColor: [
                                'rgba(23, 232, 255, 0.7)',
                                'rgba(7, 156, 214, 0.7)',
                                'rgba(36, 89, 188, 0.7)'
                            ],
                            borderColor: [
                                'rgba(23, 232, 255, 1)',
                                'rgba(7, 156, 214, 1)',
                                'rgba(36, 89, 188, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    color: 'white'
                                }
                            }
                        }
                    }
                });

                // Prepare line chart data based on period
                let labels = [];
                let salesData = [];

                if (period === 'daily') {
                    // Group by hour
                    const hours = Array(24).fill(0).map((_, i) => i.toString().padStart(2, '0') + ':00');
                    salesData = Array(24).fill(0);
                    filteredOrders.forEach(order => {
                        const date = new Date(order.date);
                        const hour = date.getHours();
                        salesData[hour] += order.amount;
                    });
                    labels = hours;
                } else if (period === 'weekly') {
                    // Group by day of week
                    labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                    salesData = Array(7).fill(0);
                    filteredOrders.forEach(order => {
                        const date = new Date(order.date);
                        const day = date.getDay() === 0 ? 6 : date.getDay() - 1; // Adjust to Mon-Sun
                        salesData[day] += order.amount;
                    });
                } else if (period === 'monthly') {
                    // Group by day of month
                    const daysInMonth = new Date(filterValue.split('-')[0], filterValue.split('-')[1], 0).getDate();
                    labels = Array(daysInMonth).fill(0).map((_, i) => (i + 1).toString());
                    salesData = Array(daysInMonth).fill(0);
                    filteredOrders.forEach(order => {
                        const date = new Date(order.date);
                        const day = date.getDate() - 1;
                        salesData[day] += order.amount;
                    });
                } else if (period === 'yearly') {
                    // Group by month
                    labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    salesData = Array(12).fill(0);
                    filteredOrders.forEach(order => {
                        const date = new Date(order.date);
                        const month = date.getMonth();
                        salesData[month] += order.amount;
                    });
                }

                // Update line chart
                if (lineChart) {
                    lineChart.destroy();
                }
                lineChart = new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Sales',
                            data: salesData,
                            backgroundColor: 'rgba(23, 232, 255, 0.2)',
                            borderColor: 'rgba(23, 232, 255, 1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: 'white',
                                    callback: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: 'white'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'white'
                                }
                            }
                        }
                    }
                });
            }

            // Filter logic
            const timePeriodSelect = document.getElementById('timePeriod');
            const dateFilter = document.getElementById('dateFilter');
            const weekFilter = document.getElementById('weekFilter');
            const monthFilter = document.getElementById('monthFilter');
            const yearFilter = document.getElementById('yearFilter');
            const applyFilterBtn = document.getElementById('applyFilter');

            // Show/hide filter inputs based on period
            timePeriodSelect.addEventListener('change', function() {
                const period = this.value;
                dateFilter.style.display = period === 'daily' ? 'inline' : 'none';
                weekFilter.style.display = period === 'weekly' ? 'inline' : 'none';
                monthFilter.style.display = period === 'monthly' ? 'inline' : 'none';
                yearFilter.style.display = period === 'yearly' ? 'inline' : 'none';
            });

            // Apply filter
            applyFilterBtn.addEventListener('click', function() {
                const period = timePeriodSelect.value;
                let filterValue;

                if (period === 'daily') {
                    filterValue = dateFilter.value;
                } else if (period === 'weekly') {
                    filterValue = weekFilter.value;
                } else if (period === 'monthly') {
                    filterValue = monthFilter.value;
                } else if (period === 'yearly') {
                    filterValue = yearFilter.value;
                }

                // Filter orders and calculate totals
                let filteredOrders = orders;
                const totals = { Wash: 0, Fold: 0, Ironing: 0, All: 0 };

                if (filterValue) {
                    filteredOrders = orders.filter(order => {
                        const orderDate = new Date(order.date);
                        if (period === 'daily') {
                            const filterDate = new Date(filterValue);
                            return orderDate.toDateString() === filterDate.toDateString();
                        } else if (period === 'weekly') {
                            const [year, week] = filterValue.split('-W');
                            const filterDate = new Date(year, 0, 1);
                            filterDate.setDate(filterDate.getDate() + (week - 1) * 7 - filterDate.getDay() + 1);
                            const weekEnd = new Date(filterDate);
                            weekEnd.setDate(weekEnd.getDate() + 6);
                            return orderDate >= filterDate && orderDate <= weekEnd;
                        } else if (period === 'monthly') {
                            const [year, month] = filterValue.split('-');
                            return orderDate.getFullYear() == year && orderDate.getMonth() + 1 == month;
                        } else if (period === 'yearly') {
                            return orderDate.getFullYear() == filterValue;
                        }
                    });
                }

                // Calculate totals for filtered orders
                filteredOrders.forEach(order => {
                    totals.All += order.amount;
                    order.service_type.forEach(service => {
                        if (service === 'Wash') totals.Wash += order.amount;
                        else if (service === 'Fold') totals.Fold += order.amount;
                        else if (service === 'Ironing') totals.Ironing += order.amount;
                    });
                });

                // Update dashboard with filtered data
                updateDashboard(totals, filteredOrders, period, filterValue);
            });

            // Initial render (default to monthly)
            updateDashboard(initialTotals, orders, 'monthly', '{{ date('Y-m') }}');
        });
    </script>
</body>
</html>