/**
 * Dashboard Charts and Interactive Features
 */

(function() {
    'use strict';

    let athleteEvolutionChart = null;
    let revenueChart = null;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        setupPeriodSelector();
        setupAutoRefresh();
    });

    /**
     * Initialize all charts
     */
    function initializeCharts() {
        initializeAthleteEvolutionChart();
        initializeRevenueChart();
    }

    /**
     * Initialize Athlete Evolution Chart
     */
    function initializeAthleteEvolutionChart() {
        const ctx = document.getElementById('athleteEvolutionChart');
        if (!ctx) return;

        // Destroy existing chart if it exists
        if (athleteEvolutionChart) {
            athleteEvolutionChart.destroy();
            athleteEvolutionChart = null;
        }

        // Get initial data from backend or fetch via API
        fetchAthleteEvolutionData('6months').then(data => {
            athleteEvolutionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month_label),
                    datasets: [{
                        label: 'Novos Atletas',
                        data: data.map(item => item.count),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6,
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
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    }

    /**
     * Initialize Revenue Chart
     */
    function initializeRevenueChart() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        // Get revenue data from window object (set by Blade template)
        let revenueData = window.revenueData || [];

        // If no data in page, fetch from API
        if (revenueData.length === 0) {
            fetchRevenueData().then(data => {
                revenueData = data;
                createRevenueChart(data);
            });
        } else {
            createRevenueChart(revenueData);
        }

        function createRevenueChart(data) {
            // Destroy existing chart if it exists
            if (revenueChart) {
                revenueChart.destroy();
                revenueChart = null;
            }
            
            revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.month_label || item.month),
                    datasets: [{
                        label: 'Receita (R$)',
                        data: data.map(item => parseFloat(item.revenue) || 0),
                        backgroundColor: 'rgba(249, 115, 22, 0.8)',
                        borderColor: 'rgb(249, 115, 22)',
                        borderWidth: 1,
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
                            callbacks: {
                                label: function(context) {
                                    return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        }
                    }
                }
            });
        }
    }

    /**
     * Setup period selector for evolution chart
     */
    function setupPeriodSelector() {
        const periodSelector = document.getElementById('evolution-period');
        if (!periodSelector) return;

        periodSelector.addEventListener('change', function() {
            const period = this.value;
            fetchAthleteEvolutionData(period).then(data => {
                if (athleteEvolutionChart) {
                    athleteEvolutionChart.data.labels = data.map(item => item.month_label);
                    athleteEvolutionChart.data.datasets[0].data = data.map(item => item.count);
                    athleteEvolutionChart.update();
                }
            });
        });
    }

    /**
     * Fetch athlete evolution data from API
     */
    function fetchAthleteEvolutionData(period) {
        return fetch(`/dashboard/athlete-evolution?period=${period}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            }
            return [];
        })
        .catch(error => {
            console.error('Error fetching athlete evolution data:', error);
            return [];
        });
    }

    /**
     * Fetch revenue data from API
     */
    function fetchRevenueData() {
        return fetch('/dashboard/metrics', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            // This would need to be implemented in the backend
            // For now, return empty array
            return [];
        })
        .catch(error => {
            console.error('Error fetching revenue data:', error);
            return [];
        });
    }

    /**
     * Setup auto-refresh for metrics
     */
    function setupAutoRefresh() {
        // Refresh metrics every 5 minutes
        setInterval(function() {
            refreshMetrics();
        }, 5 * 60 * 1000);
    }

    /**
     * Refresh dashboard metrics
     */
    function refreshMetrics() {
        fetch('/dashboard/metrics', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update metric cards
                updateMetricCard('total_athletes', data.data.total_athletes);
                updateMetricCard('active_athletes', data.data.active_athletes);
                updateMetricCard('total_teams', data.data.total_teams);
                updateMetricCard('total_revenue', data.data.total_revenue);
            }
        })
        .catch(error => {
            console.error('Error refreshing metrics:', error);
        });
    }

    /**
     * Update a metric card value
     */
    function updateMetricCard(metric, value) {
        // This would update the specific metric card
        // Implementation depends on the card structure
    }
})();

