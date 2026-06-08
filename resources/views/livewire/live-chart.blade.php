<div wire:poll.1000ms="refresh" class="rounded-xl border border-slate-700/50 bg-[#1E293B] p-4">
    <h3 class="mb-4 font-['JetBrains_Mono'] text-[11px] font-medium uppercase tracking-wider text-[#94A3B8]">Grafik Live — Jarak vs Waktu (60 detik)</h3>
    <div class="relative" style="height: 250px;">
        <canvas id="sensorChart" wire:ignore></canvas>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3/dist/chartjs-plugin-annotation.min.js"></script>
    <script>
        Chart.register(ChartAnnotation);

        document.addEventListener('livewire:init', () => {
            const ctx = document.getElementById('sensorChart');
            if (!ctx) return;

            const labels = @json($chartData['labels']);
            const leftData = @json($chartData['left']);
            const rightData = @json($chartData['right']);
            const backData = @json($chartData['back']);

            window.sensorChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Kiri',
                            data: leftData,
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.1,
                            fill: false,
                        },
                        {
                            label: 'Kanan',
                            data: rightData,
                            borderColor: '#22C55E',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.1,
                            fill: false,
                        },
                        {
                            label: 'Belakang',
                            data: backData,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            pointRadius: 0,
                            tension: 0.1,
                            fill: false,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 300 },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            onClick: function(e, legendItem, legend) {
                                const index = legendItem.datasetIndex;
                                const ci = legend.chart;
                                const meta = ci.getDatasetMeta(index);

                                meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden : null;
                                ci.update();
                            },
                            labels: {
                                color: '#94A3B8',
                                font: { family: 'JetBrains Mono', size: 11 },
                                boxWidth: 12,
                                boxHeight: 2,
                                padding: 12,
                                usePointStyle: true,
                            },
                        },
                        annotation: {
                            annotations: {
                                warningLine: {
                                    type: 'line',
                                    yMin: 50,
                                    yMax: 50,
                                    borderColor: '#EAB308',
                                    borderWidth: 1,
                                    borderDash: [5, 5],
                                    label: {
                                        content: 'WARNING (50cm)',
                                        display: true,
                                        position: 'start',
                                        color: '#EAB308',
                                        font: { family: 'JetBrains Mono', size: 10 },
                                    },
                                },
                                dangerLine: {
                                    type: 'line',
                                    yMin: 20,
                                    yMax: 20,
                                    borderColor: '#EF4444',
                                    borderWidth: 1,
                                    borderDash: [5, 5],
                                    label: {
                                        content: 'DANGER (20cm)',
                                        display: true,
                                        position: 'start',
                                        color: '#EF4444',
                                        font: { family: 'JetBrains Mono', size: 10 },
                                    },
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#94A3B8',
                                font: { family: 'JetBrains Mono', size: 10 },
                                maxTicksLimit: 10,
                                maxRotation: 0,
                            },
                            grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        },
                        y: {
                            reverse: true,
                            min: 0,
                            max: 100,
                            ticks: { color: '#94A3B8', font: { family: 'JetBrains Mono', size: 10 } },
                            grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        },
                    },
                },
            });

            Livewire.on('chartUpdated', (data) => {
                if (window.sensorChart) {
                    window.sensorChart.data.labels = data.labels;
                    window.sensorChart.data.datasets[0].data = data.left;
                    window.sensorChart.data.datasets[1].data = data.right;
                    window.sensorChart.data.datasets[2].data = data.back;
                    window.sensorChart.update('none');
                }
            });
        });
    </script>
    @endpush
</div>
