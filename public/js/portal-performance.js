/**
 * Portal Performance Page JavaScript
 */

(function() {
    'use strict';

    let performanceChart = null;
    let comparisonChart = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        loadPerformanceData();
    });

    /**
     * Load performance data from API
     */
    window.loadPerformanceData = function() {
        const period = document.getElementById('period')?.value || '3months';
        const metric = document.getElementById('metric')?.value || 'all';

        fetch(`{{ route('portal.performance-data') }}?period=${period}${metric !== 'all' ? '&metric=' + metric : ''}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderPerformanceChart(data.data, metric);
                renderComparisonChart(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading performance data:', error);
        });
    };

    /**
     * Render main performance chart
     */
    function renderPerformanceChart(data, selectedMetric) {
        const ctx = document.getElementById('performance-chart');
        if (!ctx) return;

        // Destroy existing chart
        if (performanceChart) {
            performanceChart.destroy();
        }

        // Filter data if specific metric selected
        let filteredData = data;
        if (selectedMetric && selectedMetric !== 'all') {
            filteredData = { [selectedMetric]: data[selectedMetric] || [] };
        }

        // Prepare datasets
        const datasets = [];
        const colors = [
            { border: 'rgb(59, 130, 246)', background: 'rgba(59, 130, 246, 0.1)' },
            { border: 'rgb(16, 185, 129)', background: 'rgba(16, 185, 129, 0.1)' },
            { border: 'rgb(168, 85, 247)', background: 'rgba(168, 85, 247, 0.1)' },
            { border: 'rgb(245, 158, 11)', background: 'rgba(245, 158, 11, 0.1)' },
            { border: 'rgb(239, 68, 68)', background: 'rgba(239, 68, 68, 0.1)' },
        ];

        let colorIndex = 0;
        for (const [metric, values] of Object.entries(filteredData)) {
            if (!values || values.length === 0) continue;

            const dates = values.map(v => v.date);
            const chartValues = values.map(v => v.value);

            datasets.push({
                label: metric.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                data: chartValues,
                borderColor: colors[colorIndex % colors.length].border,
                backgroundColor: colors[colorIndex % colors.length].background,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
            });

            colorIndex++;
        }

        // Get all unique dates
        const allDates = new Set();
        Object.values(filteredData).forEach(values => {
            if (values) {
                values.forEach(v => allDates.add(v.date));
            }
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
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1);
                            }
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

    /**
     * Render comparison chart (bar chart)
     */
    function renderComparisonChart(data) {
        const ctx = document.getElementById('comparison-chart');
        if (!ctx) return;

        // Destroy existing chart
        if (comparisonChart) {
            comparisonChart.destroy();
        }

        // Calculate averages for each metric
        const metrics = [];
        const averages = [];
        const colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
        ];

        let colorIndex = 0;
        for (const [metric, values] of Object.entries(data)) {
            if (!values || values.length === 0) continue;

            const avg = values.reduce((sum, v) => sum + v.value, 0) / values.length;
            metrics.push(metric.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()));
            averages.push(avg);
            colorIndex++;
        }

        comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: metrics,
                datasets: [{
                    label: 'Média',
                    data: averages,
                    backgroundColor: colors.slice(0, metrics.length),
                    borderColor: colors.slice(0, metrics.length).map(c => c.replace('0.8', '1')),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Média: ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(1);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                        }
                    }
                }
            }
        });
    }
})();

