/**
 * Portal Dashboard JavaScript
 */

(function() {
    'use strict';

    let performanceChart = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializePerformanceChart();
        
        // Period selector change
        const periodSelector = document.getElementById('performance-period');
        if (periodSelector) {
            periodSelector.addEventListener('change', function() {
                loadPerformanceData(this.value);
            });
        }
    });

    /**
     * Initialize performance chart
     */
    function initializePerformanceChart() {
        const ctx = document.getElementById('performance-chart');
        if (!ctx) return;

        const period = document.getElementById('performance-period')?.value || '3months';
        loadPerformanceData(period);
    }

    /**
     * Load performance data from API
     */
    function loadPerformanceData(period) {
        fetch(`{{ route('portal.performance-data') }}?period=${period}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPerformanceChart(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading performance data:', error);
        });
    }

    /**
     * Render performance chart
     */
    function renderPerformanceChart(data) {
        const ctx = document.getElementById('performance-chart');
        if (!ctx) return;

        // Destroy existing chart
        if (performanceChart) {
            performanceChart.destroy();
        }

        // Prepare datasets
        const datasets = [];
        const colors = [
            { border: 'rgb(59, 130, 246)', background: 'rgba(59, 130, 246, 0.1)' },
            { border: 'rgb(16, 185, 129)', background: 'rgba(16, 185, 129, 0.1)' },
            { border: 'rgb(168, 85, 247)', background: 'rgba(168, 85, 247, 0.1)' },
            { border: 'rgb(245, 158, 11)', background: 'rgba(245, 158, 11, 0.1)' },
        ];

        let colorIndex = 0;
        for (const [metric, values] of Object.entries(data)) {
            if (values.length === 0) continue;

            const dates = values.map(v => v.date);
            const chartValues = values.map(v => v.value);

            datasets.push({
                label: metric.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                data: chartValues,
                borderColor: colors[colorIndex % colors.length].border,
                backgroundColor: colors[colorIndex % colors.length].background,
                tension: 0.4,
                fill: false,
            });

            colorIndex++;
        }

        // Get all unique dates
        const allDates = new Set();
        Object.values(data).forEach(values => {
            values.forEach(v => allDates.add(v.date));
        });
        const sortedDates = Array.from(allDates).sort();

        performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: sortedDates.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('pt-BR', { month: 'short', day: 'numeric' });
                }),
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
})();

