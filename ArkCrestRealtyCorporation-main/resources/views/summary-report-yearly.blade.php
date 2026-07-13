@extends('layouts.dashboard')

@section('title', 'Yearly Summary Report')

@section('content')

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="summary-report-page">
    <!-- Page Title -->
    <div class="page-header" style="text-align: center; margin-bottom: 30px; padding: 30px; background: linear-gradient(135deg, rgba(30, 69, 117, 0.08), rgba(163, 121, 41, 0.08)); border-radius: 16px; border: 3px solid #1e4575; box-shadow: 0 8px 24px rgba(30, 69, 117, 0.12);">
        <h2 class="page-title" style="font-size: 36px; font-weight: 700; color: #1e4575; text-transform: uppercase; letter-spacing: 1.5px; background: linear-gradient(135deg, #1e4575, #A37929); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin: 0;">Summary Report</h2>
    </div>

    <!-- View Toggle Buttons -->
    <div id="viewToggleContainer" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div id="periodSelectorContainer">
            <div>
                <label style="font-weight: 600; margin-bottom: 8px; display: block;">Select Period</label>
                <select id="yearSelect" class="form-control" style="width: 250px;" onchange="changeYear()">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="view-toggle" style="display: flex; gap: 10px; background: #f0f2f5; padding: 4px; border-radius: 8px;">
            <button id="monthlyViewBtn" class="view-btn" onclick="switchToMonthly()">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Monthly View
            </button>
            <button id="yearlyViewBtn" class="view-btn active" style="cursor: default;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Yearly View
            </button>
        </div>
    </div>

    <style>
    /* View Toggle */
    .view-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border: none;
        background: transparent;
        color: #6b7280;
        font-weight: 500;
        font-size: 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .view-btn:hover {
        background: #e5e7eb;
    }

    .view-btn.active {
        background: white;
        color: #1e4575;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Yearly Table Styles */
    .yearly-table-container {
        background: white;
        border-radius: 16px;
        border: 3px solid #1e4575;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(30, 69, 117, 0.15);
    }

    .yearly-table-wrapper {
        overflow-x: auto;
        max-height: calc(100vh - 300px);
        overflow-y: auto;
    }

    .yearly-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: 1600px;
    }

    .yearly-table thead th {
        position: sticky;
        top: 0;
        background: linear-gradient(135deg, #1e4575 0%, #2c5a8f 50%, #1e4575 100%);
        color: white;
        padding: 12px 8px;
        text-align: center;
        font-weight: 700;
        border-right: 2px solid #A37929;
        border-bottom: 3px solid #A37929;
        z-index: 2;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 11px;
    }

    .yearly-table thead th:last-child {
        border-right: none;
    }

    .yearly-table tbody td {
        padding: 10px 8px;
        text-align: center;
        border-right: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        color: #1e4575;
        font-weight: 600;
        font-size: 12px;
    }

    .yearly-table tbody td:last-child {
        border-right: none;
    }

    .yearly-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(163, 121, 41, 0.1), rgba(163, 121, 41, 0.05), rgba(163, 121, 41, 0.1));
    }

    .yearly-table tbody tr:nth-child(even) {
        background: linear-gradient(90deg, rgba(248, 249, 250, 0.5), rgba(255, 255, 255, 0.5));
    }

    .yearly-table tfoot td {
        padding: 12px 8px;
        text-align: center;
        border-right: 1px solid #A37929;
        border-top: 3px solid #A37929;
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        color: #1e4575;
        font-weight: 700;
        font-size: 13px;
    }

    .yearly-table tfoot td:last-child {
        border-right: none;
    }

    .month-label {
        font-weight: 700;
        color: #1e4575;
        text-align: left !important;
        padding-left: 20px !important;
    }

    .total-row {
        background: linear-gradient(135deg, rgba(163, 121, 41, 0.15), rgba(163, 121, 41, 0.08)) !important;
    }
    </style>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="summary-card">
            <div class="card-icon" style="background: #3b82f6;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="card-content">
                <div class="card-label">Total Expenses</div>
                <div class="card-value"><span style="font-size: 20px; margin-right: 4px;">₱</span>{{ number_format($yearlyTotals['total_expenses'], 2) }}</div>
            </div>
        </div>

        <div class="summary-card">
            <div class="card-icon" style="background: #10b981;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <div class="card-content" style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <div class="card-label">Units</div>
                    <div class="card-value">{{ number_format($yearlyTotals['units'], 0) }}</div>
                </div>
                <div>
                    <div class="card-label">Gross Sales</div>
                    <div class="card-value"><span style="font-size: 20px; margin-right: 4px;">₱</span>{{ number_format($yearlyTotals['gross_sales'], 2) }}</div>
                </div>
            </div>
        </div>

        <div class="summary-card">
            <div class="card-icon" style="background: #f59e0b;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div class="card-content">
                <div class="card-label">Net Sales</div>
                <div class="card-value"><span style="font-size: 20px; margin-right: 4px;">₱</span>{{ number_format($yearlyTotals['net_sales'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-grid">
        <!-- Bar Chart -->
        <div class="chart-card">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e4575; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Department Expenses Breakdown
            </h3>
            <div class="chart-canvas-wrap">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="chart-card">
            <h3 style="font-size: 16px; font-weight: 600; color: #1e4575; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                </svg>
                Expense Distribution
            </h3>
            <div class="chart-canvas-wrap" style="display: flex; align-items: center; justify-content: center;">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    <style>
    /* Chart Cards */
    .charts-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        min-width: 0;
    }

    .chart-canvas-wrap {
        position: relative;
        height: 350px;
    }

    @media (max-width: 1024px) {
        .chart-canvas-wrap { height: 300px; }
        .chart-card { padding: 18px; }
    }

    @media (max-width: 768px) {
        .charts-grid { grid-template-columns: 1fr; gap: 16px; }
        .chart-canvas-wrap { height: 280px; }
    }

    @media (max-width: 480px) {
        .chart-canvas-wrap { height: 240px; }
        .chart-card { padding: 14px; }
    }

    .summary-card {
        background: white;
        border-radius: 12px;

    
    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        min-width: 0;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
    }

    .card-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .card-icon svg {
        width: 26px;
        height: 26px;
    }

    .card-content {
        flex: 1;
        min-width: 0;
    }

    .card-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .card-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e4575;
        display: flex;
        align-items: baseline;
    }
    </style>

    <!-- Financial Summary Table -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
        <h3 style="font-size: 16px; font-weight: 600; color: #1e4575; margin-bottom: 20px;">Financial Summary</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tbody>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; font-weight: 600; color: #1e4575; width: 250px;">Beginning Balance</td>
                    <td style="padding: 12px;">
                        <input type="number" id="beginning_balance" class="financial-input" value="0" step="0.01" oninput="calculateFinancials()">
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; font-weight: 600; color: #1e4575;">Gross Sales</td>
                    <td style="padding: 12px;">
                        <span class="financial-value" id="gross_sales_display">₱ {{ number_format($yearlyTotals['gross_sales'], 2) }}</span>
                    </td>
                </tr>
                <tr style="border-bottom: 2px solid #A37929; background: rgba(163, 121, 41, 0.05);">
                    <td style="padding: 12px; font-weight: 700; color: #A37929; font-size: 15px;">Expenses</td>
                    <td style="padding: 12px;"></td>
                </tr>
                @foreach($departments as $deptKey => $deptName)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 10px 12px 10px 40px; font-weight: 500; color: #6b7280; font-size: 14px;">{{ $deptName }}</td>
                    <td style="padding: 10px 12px;">
                        <span class="financial-value" style="color: #ef4444;">₱ {{ number_format($yearlyTotals[$deptKey], 2) }}</span>
                    </td>
                </tr>
                @endforeach
                <tr style="border-bottom: 2px solid #1e4575; background: rgba(239, 68, 68, 0.05);">
                    <td style="padding: 12px; font-weight: 700; color: #1e4575;">Total Expenses</td>
                    <td style="padding: 12px;">
                        <span class="financial-value" style="font-weight: 700; color: #ef4444;" id="expenses_display">₱ {{ number_format($yearlyTotals['total_expenses'], 2) }}</span>
                    </td>
                </tr>
                <tr style="border-bottom: 2px solid #1e4575; background: rgba(30, 69, 117, 0.05);">
                    <td style="padding: 12px; font-weight: 700; color: #1e4575;">Income Before Tax</td>
                    <td style="padding: 12px;">
                        <span class="financial-value" style="font-weight: 700; color: #10b981;" id="income_before_tax">₱ {{ number_format($yearlyTotals['net_sales'], 2) }}</span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px; font-weight: 600; color: #1e4575;">Tax Percent (%)</td>
                    <td style="padding: 12px;">
                        <input type="number" id="tax_percent" class="financial-input" value="0" step="0.01" min="0" max="100" oninput="calculateFinancials()">
                    </td>
                </tr>
                <tr style="border-bottom: 2px solid #1e4575; background: rgba(163, 121, 41, 0.05);">
                    <td style="padding: 12px; font-weight: 700; color: #1e4575;">Income After Tax</td>
                    <td style="padding: 12px;">
                        <span class="financial-value" style="font-weight: 700; color: #A37929;" id="income_after_tax">₱ 0.00</span>
                    </td>
                </tr>
                <tr style="background: rgba(30, 69, 117, 0.08);">
                    <td style="padding: 12px; font-weight: 700; color: #1e4575; font-size: 16px;">Ending Balance</td>
                    <td style="padding: 12px;">
                        <span class="financial-value" style="font-weight: 700; color: #1e4575; font-size: 18px;" id="ending_balance">₱ 0.00</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <style>
    .financial-input {
        width: 200px;
        padding: 8px 12px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        color: #1e4575;
    }

    .financial-input:focus {
        outline: none;
        border-color: #A37929;
        box-shadow: 0 0 0 3px rgba(163, 121, 41, 0.1);
    }

    .financial-value {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
    }
    </style>
</div>

<script>
function changeYear() {
    const year = document.getElementById('yearSelect').value;
    window.location.href = '{{ route("summary-report.yearly") }}?year=' + year;
}

function switchToMonthly() {
    window.location.href = '{{ route("summary-report") }}';
}

function calculateFinancials() {
    const beginningBalance = parseFloat(document.getElementById('beginning_balance').value) || 0;
    const grossSales = {{ $yearlyTotals['gross_sales'] }};
    const expenses = {{ $yearlyTotals['total_expenses'] }};
    const taxPercent = parseFloat(document.getElementById('tax_percent').value) || 0;
    
    // Calculate Income Before Tax
    const incomeBeforeTax = grossSales - expenses;
    
    // Calculate Tax Amount
    const taxAmount = (incomeBeforeTax * taxPercent) / 100;
    
    // Calculate Income After Tax
    const incomeAfterTax = incomeBeforeTax - taxAmount;
    
    // Calculate Ending Balance
    const endingBalance = beginningBalance + incomeAfterTax;
    
    // Update displays
    document.getElementById('income_before_tax').textContent = '₱ ' + incomeBeforeTax.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('income_after_tax').textContent = '₱ ' + incomeAfterTax.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('ending_balance').textContent = '₱ ' + endingBalance.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Initialize financial calculations
    calculateFinancials();
    
    // Department data
    const departments = @json(array_keys($departments));
    const departmentLabels = @json(array_values($departments));
    const departmentExpenses = [
        @foreach($departments as $deptKey => $deptName)
            {{ $yearlyTotals[$deptKey] }},
        @endforeach
    ];

    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: departmentLabels,
            datasets: [{
                label: 'Expenses',
                data: departmentExpenses,
                backgroundColor: colors,
                borderRadius: 8,
                barPercentage: 0.85,
                categoryPercentage: 0.9
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Expenses: ₱' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        },
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    });

    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: departmentLabels,
            datasets: [{
                data: departmentExpenses,
                backgroundColor: colors,
                borderWidth: 3,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        font: {
                            size: 10,
                            weight: '500'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(2);
                            return context.label + ': ' + percentage + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
