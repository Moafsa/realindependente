/**
 * Dashboard Charts and Interactive Features
 * Version 2.0 - Premium Aesthetics
 */

(function() {
    'use strict';

    let athleteEvolutionChart = null;
    let revenueChart = null;

    // Chart Colors
    const colors = {
        blue: {
            solid: 'rgb(59, 130, 246)',
            light: 'rgba(59, 130, 246, 0.1)',
            gradient: ['rgba(59, 130, 246, 0.3)', 'rgba(59, 130, 246, 0)']
        },
        orange: {
            solid: 'rgb(249, 115, 22)',
            light: 'rgba(249, 115, 22, 0.1)',
            gradient: ['rgba(249, 115, 22, 0.3)', 'rgba(249, 115, 22, 0)']
        }
    };

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();
        setupPeriodSelector();
        setupAutoRefresh();
        animateNumbers();
    });

    /**
     * Animate Metric Numbers
     */
    function animateNumbers() {
        const counters = document.querySelectorAll('.text-3xl.font-bold');
        counters.forEach(counter => {
            const animate = () => {
                const value = counter.innerText;
                const isPrice = value.includes('R$');
                let target = parseFloat(value.replace('R$', '').replace('.', '').replace(',', '.'));
                
                if (isNaN(target)) return;

                let current = 0;
                const step = target / 50;
                
                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        if (isPrice) {
                            counter.innerText = 'R$ ' + Math.floor(current).toLocaleString('pt-BR');
                        } else {
                            counter.innerText = Math.floor(current);
                        }
                        setTimeout(updateCounter, 20);
                    } else {
                        counter.innerText = value;
                    }
                };
                updateCounter();
            };
            animate();
        });
    }

    /**
     * Initialize all charts
     */
    function initializeCharts() {
        initializeAthleteEvolutionChart();
        initializeRevenueChart();
    }

    /**
     * Helper to create gradient
     */
    function createGradient(ctx, colors) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, colors[0]);
        gradient.addColorStop(1, colors[1]);
        return gradient;
    }

    /**
     * Initialize Athlete Evolution Chart
     */
    function initializeAthleteEvolutionChart() {
        const ctx = document.getElementById('athleteEvolutionChart');
        if (!ctx) return;

        fetchAthleteEvolutionData('6months').then(data => {
            const chartCtx = ctx.getContext('2d');
            const bgGradient = createGradient(chartCtx, colors.blue.gradient);

            athleteEvolutionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month_label),
                    datasets: [{
                        label: 'Evolução de Atletas',
                        data: data.map(item => item.count || item.avg_score || 0),
                        borderColor: colors.blue.solid,
                        backgroundColor: bgGradient,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2,
                        pointHoverRadius: 8,
                        pointHoverBorderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ' Score Médio: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
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

        let revenueData = window.revenueData || [];

        if (revenueData.length === 0) {
            // Se não houver dados no window, buscar da API (ou usar fallback do window injetado pelo Blade)
            createRevenueChartFromView(window.revenueTrendsData || []);
        } else {
            createRevenueChartFromView(revenueData);
        }

        function createRevenueChartFromView(data) {
            if (revenueChart) revenueChart.destroy();
            
            const chartCtx = ctx.getContext('2d');
            const bgGradient = createGradient(chartCtx, colors.orange.gradient);

            revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.month_label || item.month),
                    datasets: [{
                        label: 'Receita',
                        data: data.map(item => parseFloat(item.revenue) || 0),
                        backgroundColor: bgGradient,
                        borderColor: colors.orange.solid,
                        borderWidth: 2,
                        borderRadius: 6,
                        hoverBackgroundColor: colors.orange.solid,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            callbacks: {
                                label: function(context) {
                                    return ' R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                        minimumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: {
                                callback: val => 'R$ ' + val.toLocaleString('pt-BR'),
                                font: { size: 10 }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    }

    /**
     * Period Selector Setup
     */
    function setupPeriodSelector() {
        const selector = document.getElementById('evolution-period');
        if (!selector) return;

        selector.addEventListener('change', function() {
            fetchAthleteEvolutionData(this.value).then(data => {
                if (athleteEvolutionChart) {
                    athleteEvolutionChart.data.labels = data.map(item => item.month_label);
                    athleteEvolutionChart.data.datasets[0].data = data.map(item => item.count || item.avg_score);
                    athleteEvolutionChart.update();
                }
            });
        });
    }

    /**
     * API Fetchers
     */
    function fetchAthleteEvolutionData(period) {
        return fetch(`/dashboard/athlete-evolution?period=${period}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(json => json.success ? json.data : [])
        .catch(() => []);
    }

    /**
     * Polling logic
     */
    function setupAutoRefresh() {
        setInterval(() => {
            fetch('/dashboard/metrics', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(json => {
                if (json.success) updateUI(json.data);
            });
        }, 60000); // 1 minute
    }

    function updateUI(data) {
        // Update counters with smooth transition
        updateValue('total_athletes', data.total_athletes);
        updateValue('active_athletes', data.active_athletes);
        updateValue('total_teams', data.total_teams);
        updateValue('this_month_revenue', 'R$ ' + data.monthly_revenue.toLocaleString('pt-BR', { minimumFractionDigits: 2 }));
    }

    function updateValue(id, value) {
        const el = document.getElementById('metric-' + id);
        if (el && el.innerText != value) {
            el.classList.add('scale-110', 'text-blue-600');
            setTimeout(() => {
                el.innerText = value;
                el.classList.remove('scale-110', 'text-blue-600');
            }, 300);
        }
    }
})();

