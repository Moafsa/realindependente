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
            filteredData = { [selectedMetric]: data[selectedMetric] || { athlete: [], category_avg: [] } };
        }

        // Prepare datasets
        const datasets = [];
        const colors = [
            { border: 'rgb(59, 130, 246)', background: 'rgba(59, 130, 246, 0.1)', category: 'rgba(59, 130, 246, 0.4)' },
            { border: 'rgb(16, 185, 129)', background: 'rgba(16, 185, 129, 0.1)', category: 'rgba(16, 185, 129, 0.4)' },
            { border: 'rgb(168, 85, 247)', background: 'rgba(168, 85, 247, 0.1)', category: 'rgba(168, 85, 247, 0.4)' },
            { border: 'rgb(245, 158, 11)', background: 'rgba(245, 158, 11, 0.1)', category: 'rgba(245, 158, 11, 0.4)' },
            { border: 'rgb(239, 68, 68)', background: 'rgba(239, 68, 68, 0.1)', category: 'rgba(239, 68, 68, 0.4)' },
        ];

        let colorIndex = 0;
        for (const [metric, values] of Object.entries(filteredData)) {
            const metricName = metric.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            const color = colors[colorIndex % colors.length];

            // Athlete Data
            if (values.athlete && values.athlete.length > 0) {
                datasets.push({
                    label: `${metricName} (Você)`,
                    data: values.athlete.map(v => ({ x: v.date, y: v.value })),
                    borderColor: color.border,
                    backgroundColor: color.background,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                });
            }

            // Category Average Data
            if (values.category_avg && values.category_avg.length > 0) {
                datasets.push({
                    label: `${metricName} (Média Categoria)`,
                    data: values.category_avg.map(v => ({ x: v.date, y: v.value })),
                    borderColor: color.category,
                    backgroundColor: 'transparent',
                    borderDash: [5, 5],
                    tension: 0.4,
                    fill: false,
                    pointRadius: 0,
                });
            }

            colorIndex++;
        }

        performanceChart = new Chart(ctx, {
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        type: 'category',
                        grid: { display: false }
                    }
                }
            },
            data: {
                datasets: datasets
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

        const metrics = [];
        const athleteAverages = [];
        const categoryAverages = [];

        for (const [metric, values] of Object.entries(data)) {
            const metricName = metric.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            metrics.push(metricName);

            const athleteAvg = values.athlete.length > 0 
                ? values.athlete.reduce((sum, v) => sum + v.value, 0) / values.athlete.length 
                : 0;
            
            const categoryAvg = values.category_avg.length > 0
                ? values.category_avg.reduce((sum, v) => sum + v.value, 0) / values.category_avg.length
                : 0;

            athleteAverages.push(athleteAvg);
            categoryAverages.push(categoryAvg);
        }

        comparisonChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: metrics,
                datasets: [
                    {
                        label: 'Seu Desempenho',
                        data: athleteAverages,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    },
                    {
                        label: 'Média da Categoria',
                        data: categoryAverages,
                        backgroundColor: 'rgba(156, 163, 175, 0.5)',
                        borderColor: 'rgb(156, 163, 175)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
})();

