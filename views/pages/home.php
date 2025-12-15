<?php
$activePage = 'home';
$pageTitle = 'Home';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="row" style="display: flex; gap: 20px; flex-wrap: wrap;">
    <!-- Left Column: Live Visitor Count -->
    <div style="flex: 1; min-width: 300px;">
        <div class="card" style="height: 100%; display: flex; flex-direction: column;">
            <h2>Live Status</h2>
            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 20px;">
                <div style="font-size: 5rem; font-weight: 800; color: var(--primary-color); line-height: 1; margin-bottom: 10px;">
                    <?php echo $current_live ?? 0; ?>
                </div>
                <div class="text-muted" style="font-size: 1.1rem; font-weight: 500;">Active Visitors</div>
                <div class="text-muted" style="font-size: 0.85rem; margin-top: 5px;">(Last 30 Minutes)</div>
            </div>
        </div>
    </div>

    <!-- Right Column: Visitor Trend Graph -->
    <div style="flex: 2; min-width: 400px;">
        <div class="card" style="height: 100%;">
            <h2>Visitor Trend (Last 24 Hours)</h2>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="homeVisitorChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 20px;">
    <h2>Quick Start</h2>
    <p>Use the <strong>Campaigns</strong> menu on the left to manage your widgets and notifications.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxHome = document.getElementById('homeVisitorChart').getContext('2d');
    const homeVisitorChart = new Chart(ctxHome, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels ?? []); ?>,
            datasets: [{
                label: 'Peak Visitors (per Hour)',
                data: <?php echo json_encode($counts ?? []); ?>,
                backgroundColor: 'rgba(0, 132, 255, 0.6)',
                borderColor: 'rgba(0, 132, 255, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barPercentage: 0.6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
