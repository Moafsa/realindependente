/**
 * Athlete Profile Tabs and Charts
 */

(function() {
    'use strict';

    let performanceChart = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeTabs();
        if (window.athleteId) {
            initializePerformanceChart();
        }
    });

    /**
     * Initialize tab navigation
     */
    function initializeTabs() {
        // Get initial tab from URL hash or default to profile
        const hash = window.location.hash.replace('#', '');
        const initialTab = hash && ['profile', 'performance', 'financial', 'ai-plans'].includes(hash) 
            ? hash 
            : 'profile';
        
        showTab(initialTab);
    }

    /**
     * Show a specific tab
     */
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active', 'border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        const content = document.getElementById(`content-${tabName}`);
        if (content) {
            content.classList.remove('hidden');
        }

        // Activate selected tab button
        const button = document.getElementById(`tab-${tabName}`);
        if (button) {
            button.classList.add('active', 'border-blue-500', 'text-blue-600');
            button.classList.remove('border-transparent', 'text-gray-500');
        }

        // Update URL hash
        window.location.hash = tabName;

        // Initialize chart if performance tab
        if (tabName === 'performance' && !performanceChart) {
            initializePerformanceChart();
        }
    }

    // Make showTab available globally
    window.showTab = showTab;

    /**
     * Initialize performance chart
     */
    function initializePerformanceChart() {
        const ctx = document.getElementById('performanceChart');
        if (!ctx || !window.athleteId) return;

        const periodSelector = document.getElementById('performance-period');
        const period = periodSelector ? periodSelector.value : '6months';

        fetch(`/athletes/${window.athleteId}/performance-data?period=${period}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                createPerformanceChart(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading performance data:', error);
        });

        // Update chart when period changes
        if (periodSelector) {
            periodSelector.addEventListener('change', function() {
                const newPeriod = this.value;
                fetch(`/athletes/${window.athleteId}/performance-data?period=${newPeriod}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data && performanceChart) {
                        updatePerformanceChart(data.data);
                    }
                });
            });
        }
    }

    /**
     * Create performance chart
     */
    function createPerformanceChart(data) {
        const ctx = document.getElementById('performanceChart');
        if (!ctx || !window.Chart) return;

        // Group data by metric
        const metrics = {};
        const labels = new Set();

        Object.keys(data).forEach(metric => {
            metrics[metric] = [];
            data[metric].forEach(point => {
                labels.add(point.date);
            });
        });

        const sortedLabels = Array.from(labels).sort();

        // Prepare datasets
        const datasets = Object.keys(metrics).map((metric, index) => {
            const values = sortedLabels.map(label => {
                const point = data[metric].find(p => p.date === label);
                return point ? parseFloat(point.value) : null;
            });

            const colors = [
                'rgb(59, 130, 246)',   // blue
                'rgb(16, 185, 129)',   // green
                'rgb(245, 101, 101)',  // red
                'rgb(251, 191, 36)',   // yellow
                'rgb(139, 92, 246)',   // purple
            ];

            return {
                label: metric,
                data: values,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
                tension: 0.4,
                fill: false,
            };
        });

        performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: sortedLabels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
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
                    }
                }
            }
        });
    }

    /**
     * Update performance chart with new data
     */
    function updatePerformanceChart(data) {
        if (!performanceChart) {
            createPerformanceChart(data);
            return;
        }

        // Similar logic to createPerformanceChart but update existing chart
        const metrics = {};
        const labels = new Set();

        Object.keys(data).forEach(metric => {
            data[metric].forEach(point => {
                labels.add(point.date);
            });
        });

        const sortedLabels = Array.from(labels).sort();
        const datasets = Object.keys(data).map((metric, index) => {
            const values = sortedLabels.map(label => {
                const point = data[metric].find(p => p.date === label);
                return point ? parseFloat(point.value) : null;
            });

            const colors = [
                'rgb(59, 130, 246)',
                'rgb(16, 185, 129)',
                'rgb(245, 101, 101)',
                'rgb(251, 191, 36)',
                'rgb(139, 92, 246)',
            ];

            return {
                label: metric,
                data: values,
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length].replace('rgb', 'rgba').replace(')', ', 0.1)'),
                tension: 0.4,
                fill: false,
            };
        });

        performanceChart.data.labels = sortedLabels;
        performanceChart.data.datasets = datasets;
        performanceChart.update();
    }
})();

