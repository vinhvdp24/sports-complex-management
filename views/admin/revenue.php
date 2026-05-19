<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm p-4">
                <h3 class="text-primary mb-4">📊 Thống Kê Doanh Thu</h3>

                <!-- Date Filter Form -->
                <form class="row g-3 align-items-end mb-4" method="GET" action="<?= BASE_URL ?>admin/revenue">
                    <div class="col-md-4">
                        <label for="startDate" class="form-label">Ngày Bắt Đầu:</label>
                        <input type="date" class="form-control" id="startDate" name="startDate" value="<?= htmlspecialchars($startDate ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="endDate" class="form-label">Ngày Kết Thúc:</label>
                        <input type="date" class="form-control" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <a href="<?= BASE_URL ?>admin/revenue" class="btn btn-secondary w-100">Xem Tất Cả</a>
                    </div>
                </form>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-success text-white p-3">
                            <h5>Doanh Thu Sân:</h5>
                            <p class="display-6"><?= number_format($revenueData['pitchRevenue'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark p-3">
                            <h5>Doanh Thu Dịch Vụ:</h5>
                            <p class="display-6"><?= number_format($revenueData['serviceRevenue'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white p-3">
                            <h5>Doanh Thu Bán Hàng:</h5>
                            <p class="display-6"><?= number_format($revenueData['productRevenue'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white p-3">
                            <h5>Đã Hủy:</h5>
                            <p class="display-6"><?= number_format($cancelledRevenue ?? 0, 0, ',', '.') ?> VNĐ</p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary">
                    <h4>Tổng Doanh Thu:
                        <span class="text-danger fw-bold"><?= number_format($revenueData['totalRevenue'], 0, ',', '.') ?> VNĐ</span>
                    </h4>
                </div>

                <hr>

                <h4 class="mb-3">Biểu Đồ Doanh Thu</h4>
                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <canvas id="revenuePieChart"></canvas>
                    </div>
                </div>

                <hr class="my-5">

                <h4 class="mb-3">Biểu Đồ Số Lượng Đặt Sân Theo Ngày</h4>
                <div class="row">
                    <div class="col-12">
                        <canvas id="bookingLineChart"></canvas>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pie Chart
    const revenuePieChartCanvas = document.getElementById('revenuePieChart');
    if (revenuePieChartCanvas) {
        new Chart(revenuePieChartCanvas, {
            type: 'pie',
            data: {
                labels: ['Doanh Thu Sân', 'Doanh Thu Dịch Vụ', 'Doanh Thu Bán Hàng'],
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: [
                        <?= $revenueData['pitchRevenue'] ?? 0 ?>,
                        <?= $revenueData['serviceRevenue'] ?? 0 ?>,
                        <?= $revenueData['productRevenue'] ?? 0 ?>
                    ],
                    backgroundColor: [
                        'rgba(108, 117, 125, 0.7)', // Gray
                        'rgba(220, 53, 69, 0.7)',  // Red
                        'rgba(0, 123, 255, 0.7)'   // Blue
                    ],
                    borderColor: [
                        'rgba(108, 117, 125, 1)',
                        'rgba(220, 53, 69, 1)',
                        'rgba(0, 123, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw;
                                return tooltipItem.label + ': ' + value.toLocaleString('vi-VN') + ' VNĐ';
                            }
                        }
                    }
                }
            }
        });
    }

    // Line Chart
    const bookingLineChartCanvas = document.getElementById('bookingLineChart');
    if (bookingLineChartCanvas) {
        const bookingData = <?= json_encode($bookingCountData) ?>;
        const labels = bookingData.map(item => item.NgayDat);
        const data = bookingData.map(item => item.booking_count);

        new Chart(bookingLineChartCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Số Lượng Đặt Sân',
                    data: data,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Ensure y-axis increments by whole numbers
                        }
                    }
                },
                plugins: {
                    zoom: {
                        pan: {
                            enabled: true,
                            mode: 'x',
                        },
                        zoom: {
                            wheel: {
                                enabled: true,
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'x',
                        }
                    },
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Số sân đã đặt: ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>