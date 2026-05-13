/**
 * Athlete Profile Tabs and Charts
 */

(function() {
    'use strict';

    let performanceChart = null;
    let currentChartType = 'radar';

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeTabs();
    });

    window.changeChartType = function(type) {
        currentChartType = type;
        
        // Update UI buttons
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'shadow-sm', 'text-blue-600');
            btn.classList.add('text-gray-500');
        });
        
        const activeBtn = document.getElementById('btn-' + type);
        if (activeBtn) {
            activeBtn.classList.add('bg-white', 'shadow-sm', 'text-blue-600');
            activeBtn.classList.remove('text-gray-500');
        }
        
        initializePerformanceChart();
    };

    /**
     * Initialize tab navigation
     */
    function initializeTabs() {
        // Get initial tab from URL hash or default to profile
        const hash = window.location.hash.replace('#', '');
        const initialTab = hash && ['profile', 'performance', 'financial', 'ai-plans', 'documents'].includes(hash) 
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
            initializeSparklines();
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
     * Create performance chart using ApexCharts
     */
    function createPerformanceChart(data) {
        const ctx = document.getElementById('performanceChart');
        if (!ctx || typeof ApexCharts === 'undefined') return;

        // Process data based on chart type
        let series = [];
        let sortedLabels = [];
        const metricsList = Object.keys(data);

        if (currentChartType === 'line') {
            // Group data by date for Line chart
            const labelsSet = new Set();
            metricsList.forEach(metric => {
                if (data[metric] && data[metric].athlete) {
                    data[metric].athlete.forEach(point => labelsSet.add(point.date));
                }
            });
            
            sortedLabels = Array.from(labelsSet).sort();
            
            series = metricsList.map(metric => ({
                name: metric,
                data: sortedLabels.map(date => {
                    const point = data[metric].athlete.find(p => p.date === date);
                    return point ? parseFloat(point.value) : null;
                })
            }));
        } else {
            // Radar or Bar
            const latestValues = [];
            const averageValues = [];

            metricsList.forEach(metric => {
                const athletePoints = data[metric].athlete.sort((a, b) => new Date(b.date) - new Date(a.date));
                const categoryPoints = data[metric].category_avg.sort((a, b) => new Date(b.date) - new Date(a.date));
                
                latestValues.push(athletePoints[0] ? parseFloat(athletePoints[0].value) : 0);
                averageValues.push(categoryPoints[0] ? parseFloat(categoryPoints[0].value) : 0);
            });

            series = [
                { name: 'Nível do Atleta', data: latestValues },
                { name: 'Média da Equipe', data: averageValues }
            ];
            sortedLabels = metricsList;
        }

        const options = {
            series: series,
            chart: {
                height: 400,
                type: currentChartType,
                fontFamily: 'Figtree, sans-serif',
                toolbar: { show: false },
            },
            colors: currentChartType === 'line' ? ['#3B82F6', '#10B981', '#F56565', '#FBBF24', '#8B5CF6'] : ['#3B82F6', '#94A3B8'],
            stroke: { 
                width: currentChartType === 'line' ? 3 : [3, 2],
                curve: 'smooth',
                dashArray: currentChartType === 'line' ? 0 : [0, 4]
            },
            fill: { 
                opacity: currentChartType === 'line' ? 1 : [0.3, 0.1] 
            },
            dataLabels: {
                enabled: false
            },
            markers: { 
                size: currentChartType === 'line' ? 4 : [4, 0] 
            },
            xaxis: {
                categories: sortedLabels,
                labels: {
                    style: {
                        colors: Array(sortedLabels.length).fill('#64748b'),
                        fontSize: '11px',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                show: true,
                min: 0,
                max: 100,
                tickAmount: 5,
                labels: {
                    formatter: function(val) { return val; },
                    style: { colors: '#cbd5e1', fontSize: '10px' }
                }
            },
            plotOptions: {
                radar: {
                    size: 140,
                    polygons: {
                        strokeColors: '#e2e8f0',
                        connectorColors: '#e2e8f0',
                        fill: { colors: ['#f8fafc', '#fff'] }
                    }
                },
                bar: {
                    borderRadius: 4,
                    horizontal: false,
                    columnWidth: '55%'
                }
            },
            grid: { show: currentChartType !== 'radar' },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                offsetY: 0
            },
            tooltip: {
                y: {
                    formatter: function(val) { return val + '%' }
                }
            }
        };

        if(performanceChart) performanceChart.destroy();
        
        // Hide placeholder message if exists
        const placeholder = document.querySelector('.performance-tab-content p.text-gray-500.italic');
        if (placeholder) placeholder.style.display = 'none';

        performanceChart = new ApexCharts(document.querySelector("#performanceChart"), options);
        performanceChart.render();
    }

    /**
     * Initialize Sparklines for each indicator
     */
    function initializeSparklines() {
        const sparklineData = window.sparklineData;
        if (!sparklineData) return;

        Object.keys(sparklineData).forEach(metric => {
            const slug = metric.toLowerCase().replace(/[^a-z0-9]/g, '-');
            const container = document.getElementById('sparkline-' + slug);
            if (!container) return;

            const options = {
                series: [{
                    data: sparklineData[metric].map(p => p.y)
                }],
                chart: {
                    type: 'area',
                    height: 50,
                    sparkline: { enabled: true },
                    animations: { enabled: true }
                },
                stroke: { curve: 'smooth', width: 2 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                    }
                },
                colors: ['#3B82F6'],
                tooltip: {
                    fixed: { enabled: false },
                    x: { show: false },
                    y: {
                        title: { formatter: () => metric }
                    },
                    marker: { show: false }
                }
            };

            const chart = new ApexCharts(container, options);
            chart.render();
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

        const metricsList = Object.keys(data);
        const latestValues = metricsList.map(metric => {
            const points = data[metric].athlete;
            const latestPoint = points.sort((a, b) => new Date(b.date) - new Date(a.date))[0];
            return latestPoint ? parseFloat(latestPoint.value) : 0;
        });

        const averageValues = metricsList.map(metric => {
            const points = data[metric].category_avg;
            const latestAvg = points.sort((a, b) => new Date(b.date) - new Date(a.date))[0];
            return latestAvg ? parseFloat(latestAvg.value) : 0;
        });

        const series = [
            { name: 'Nível do Atleta', data: latestValues },
            { name: 'Média da Equipe', data: averageValues }
        ];

        const sortedLabels = metricsList;

        performanceChart.updateOptions({
            xaxis: { categories: sortedLabels }
        });
        performanceChart.updateSeries(series);
    }
})();

