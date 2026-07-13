@extends('layouts.dashboard')

@section('content')

<!-- Add Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="summary-report-page">
    <!-- Page Title with View Toggle -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 class="page-title">Summary Report</h2>
        <div class="view-toggle" style="display: flex; gap: 10px; background: #f0f2f5; padding: 4px; border-radius: 8px;">
            <button id="monthlyViewBtn" class="view-btn active" onclick="switchView('monthly')">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Monthly View
            </button>
            <button id="yearlyViewBtn" class="view-btn" onclick="switchView('yearly')">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Yearly View
            </button>
        </div>
    </div>

    <!-- Monthly View -->
    <div id="monthlyView" class="view-container">
        <!-- Month Selector -->
        <div style="margin-bottom: 20px;">
            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Select Period</label>
            <select id="periodSelector" class="form-control" style="width: 250px;" onchange="loadPeriod()">
                @foreach($availablePeriods as $period)
                    <option value="{{ $period->month }}-{{ $period->year }}" 
                            {{ ($period->month == $selectedMonth && $period->year == $selectedYear) ? 'selected' : '' }}>
                        {{ date('F Y', mktime(0, 0, 0, $period->month, 1, $period->year)) }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Summary Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="summary-card">
                <div class="card-icon" style="background: #3b82f6;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="card-content">
                    <div class="card-label">Total Expenses</div>
                    <div class="card-value">₱ {{ number_format($totalExpenses, 2) }}</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: #10b981;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div class="card-content">
                    <div class="card-label">Gross Sales</div>
                    <div class="card-value">₱ {{ number_format($summaryReport->gross_sales, 2) }}</div>
                </div>
            </div>

            <div class="summary-card">
                <div class="card-icon" style="background: #8b5cf6;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="card-content">
                    <div class="card-label">Units</div>
                    <div class="card-value">{{ number_format($summaryReport->units, 0) }}</div>
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
                    <div class="card-value">₱ {{ number_format($netSales, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <!-- Department Expenses Chart -->
            <div class="chart-card">
                <h3 class="chart-title">Department Expenses Breakdown</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="deptExpensesChart"></canvas>
                </div>
            </div>

            <!-- Expense Distribution Pie Chart -->
            <div class="chart-card">
                <h3 class="chart-title">Expense Distribution</h3>
                <div class="chart-canvas-wrap">
                    <canvas id="expenseDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Entry Section -->
        <div class="data-entry-card">
            <h3 style="margin-bottom: 20px; font-size: 18px; font-weight: 600;">Monthly Data Entry</h3>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label>Units</label>
                    <input type="number" id="units" class="form-control" step="0.01" value="{{ $summaryReport->units }}">
                </div>
                <div class="form-group">
                    <label>Gross Sales</label>
                    <input type="number" id="gross_sales" class="form-control" step="0.01" value="{{ $summaryReport->gross_sales }}">
                </div>
                <div class="form-group">
                    <label>COH</label>
                    <input type="number" id="coh" class="form-control" step="0.01" value="{{ $summaryReport->coh }}">
                </div>
            </div>
            <button onclick="saveSummaryReport()" class="btn-submit">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                Save Monthly Data
            </button>
        </div>

        <!-- Detailed Report Table -->
        <div class="report-table-card">
            <h3 style="margin-bottom: 15px; font-size: 18px; font-weight: 600;">Detailed Monthly Report</h3>
            <table class="report-table">
                <tbody>
                    @foreach($departments as $deptKey => $deptName)
                    <tr>
                        <td class="label-cell">{{ $deptName }}</td>
                        <td class="value-cell">₱ {{ number_format($departmentExpenses[$deptKey], 2) }}</td>
                    </tr>
                    @endforeach
                    <tr class="total-row">
                        <td class="label-cell">Total Expenses</td>
                        <td class="value-cell">₱ {{ number_format($totalExpenses, 2) }}</td>
                    </tr>
                    <tr class="net-sales-row">
                        <td class="label-cell">Net Sales</td>
                        <td class="value-cell" id="net_sales">₱ {{ number_format($netSales, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Yearly View (Hidden by default) -->
    <div id="yearlyView" class="view-container" style="display: none;">
        <div style="text-align: center; padding: 60px 20px;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 20px; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 style="font-size: 20px; color: #374151; margin-bottom: 8px;">Yearly View Coming Soon</h3>
            <p style="color: #6b7280;">This feature will show annual trends and comparisons</p>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastNotification" class="custom-toast">
    <div class="toast-icon" id="toastIcon"></div>
    <div class="toast-content">
        <div class="toast-title" id="toastTitle"></div>
        <div class="toast-message" id="toastMessage"></div>
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

/* Summary Cards */
.summary-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.card-icon svg {
    width: 24px;
    height: 24px;
}

.card-content {
    flex: 1;
}

.card-label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 4px;
}

.card-value {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
}

/* Chart Cards */
.chart-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.chart-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #111827;
}

/* Data Entry Card */
.data-entry-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* Report Table Card */
.report-table-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.report-table {
    width: 100%;
    border-collapse: collapse;
}

.report-table td {
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.label-cell {
    font-weight: 500;
    color: #374151;
}

.value-cell {
    text-align: right;
    font-weight: 600;
    color: #111827;
}

.total-row {
    background: #fef3c7;
}

.net-sales-row {
    background: #d1fae5;
}

/* Toast */
.custom-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    transform: translateY(10px);
    background: white;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.15);
    padding: 14px 18px;
    display: none;
    align-items: center;
    gap: 12px;
    min-width: 280px;
    max-width: 380px;
    z-index: 10000;
    border-left: 4px solid #A37929;
    transition: all 0.3s ease;
}

.custom-toast.show {
    display: flex;
    transform: translateY(0);
    opacity: 1;
}

.custom-toast.hiding {
    opacity: 0;
    transform: translateY(10px);
}

.toast-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0;
}

.custom-toast.success .toast-icon {
    background: #10b981;
    color: white;
}

.custom-toast.error .toast-icon {
    background: #ef4444;
    color: white;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
}

.toast-message {
    font-size: 13px;
    color: #6b7280;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

@keyframes fadeOut {
    from {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
    to {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.9);
    }
}
</style>

<script>
const totalExpenses = {{ $totalExpenses }};
const departmentExpenses = @json($departmentExpenses);
const departments = @json(array_keys($departmentExpenses));

// Initialize Charts
let deptChart, pieChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    calculateNetSales();
    
    // Add input listener
    document.getElementById('gross_sales').addEventListener('input', calculateNetSales);
});

function initializeCharts() {
    // Department Expenses Bar Chart
    const deptCtx = document.getElementById('deptExpensesChart').getContext('2d');
    deptChart = new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(departmentExpenses),
            datasets: [{
                label: 'Expenses',
                data: Object.values(departmentExpenses),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899'
                ],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Expense Distribution Pie Chart
    const pieCtx = document.getElementById('expenseDistributionChart').getContext('2d');
    pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(departmentExpenses),
            datasets: [{
                data: Object.values(departmentExpenses),
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6',
                    '#ec4899'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function switchView(view) {
    const monthlyView = document.getElementById('monthlyView');
    const yearlyView = document.getElementById('yearlyView');
    const monthlyBtn = document.getElementById('monthlyViewBtn');
    const yearlyBtn = document.getElementById('yearlyViewBtn');

    if (view === 'monthly') {
        monthlyView.style.display = 'block';
        yearlyView.style.display = 'none';
        monthlyBtn.classList.add('active');
        yearlyBtn.classList.remove('active');
    } else {
        monthlyView.style.display = 'none';
        yearlyView.style.display = 'block';
        monthlyBtn.classList.remove('active');
        yearlyBtn.classList.add('active');
    }
}

function calculateNetSales() {
    const grossSales = parseFloat(document.getElementById('gross_sales').value) || 0;
    const netSales = grossSales - totalExpenses;
    document.getElementById('net_sales').textContent = '₱ ' + netSales.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function loadPeriod() {
    const selector = document.getElementById('periodSelector');
    const [month, year] = selector.value.split('-');
    
    localStorage.setItem('summaryReportMonth', month);
    localStorage.setItem('summaryReportYear', year);
    
    window.location.href = `{{ route('summary-report') }}?month=${month}&year=${year}`;
}

// On page load
window.addEventListener('DOMContentLoaded', function() {
    const savedMonth = localStorage.getItem('summaryReportMonth');
    const savedYear = localStorage.getItem('summaryReportYear');
    
    const urlParams = new URLSearchParams(window.location.search);
    const urlMonth = urlParams.get('month');
    const urlYear = urlParams.get('year');
    
    if (savedMonth && savedYear && !urlMonth && !urlYear) {
        window.location.href = `{{ route('summary-report') }}?month=${savedMonth}&year=${savedYear}`;
    }
});

function saveSummaryReport() {
    const month = {{ $selectedMonth }};
    const year = {{ $selectedYear }};
    const units = parseFloat(document.getElementById('units').value) || 0;
    const grossSales = parseFloat(document.getElementById('gross_sales').value) || 0;
    const coh = parseFloat(document.getElementById('coh').value) || 0;
    
    fetch('/api/summary-report/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            month: month,
            year: year,
            units: units,
            gross_sales: grossSales,
            coh: coh
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Success', 'Summary report saved successfully!');
            calculateNetSales();
        } else {
            showToast('error', 'Error', data.message || 'Error saving summary report');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error', 'Error saving summary report');
    });
}

function showToast(type, title, message, callback) {
    const toast = document.getElementById('toastNotification');
    const icon = document.getElementById('toastIcon');
    const titleEl = document.getElementById('toastTitle');
    const messageEl = document.getElementById('toastMessage');
    
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };
    
    icon.textContent = icons[type] || icons.info;
    titleEl.textContent = title;
    messageEl.textContent = message;
    
    toast.classList.remove('success', 'error', 'warning', 'info', 'hiding');
    toast.classList.add(type);
    toast.classList.add('show');
    
    setTimeout(() => {
        toast.classList.add('hiding');
        setTimeout(() => {
            toast.classList.remove('show', 'hiding');
            if (callback) callback();
        }, 300);
    }, 2500);
}
</script>
@endsection
