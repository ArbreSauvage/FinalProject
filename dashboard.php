<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// dashboard.php
include 'db.php';

try {
    // Fetch the 10 most recent sensor readings to display in the table and chart
    $stmt = $pdo->query("SELECT temperature, humidity, timestamp FROM sensor_data ORDER BY timestamp DESC LIMIT 10");
    $readings = $stmt->fetchAll();
    
    // Reverse the array so the chart reads chronologically from left to right
    $chartData = array_reverse($readings);
    
    // Get the absolute latest reading for the metric cards
    $latest = !empty($readings) ? $readings[0] : ['temperature' => '--', 'humidity' => '--', 'timestamp' => 'No data yet'];
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivityHub - Sensor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --card-bg: #ffffff;
            --primary: #0066cc;
            --border: #dee2e6;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }
        header {
            max-width: 1000px;
            margin: 0 auto 20px auto;
            border-bottom: 2px solid var(--border);
            padding-bottom: 10px;
        }
        main {
            max-width: 1000px;
            margin: 0 auto;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .card h2 { margin: 0 0 10px 0; font-size: 1.2rem; color: #6c757d; }
        .card .value { font-size: 2.5rem; font-weight: bold; color: var(--primary); }
        .chart-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th { background-color: #f1f3f5; font-weight: 600; }
        .refresh-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .refresh-btn:hover { background-color: #0052a3; }
    </style>
</head>
<body>

<header>
    <h1>DHT11 Environment Monitor</h1>
    <p>Real-time climate telemetry for our shared database network[cite: 159, 178].</p>
    <button class="refresh-btn" onclick="window.location.reload();">🔄 Refresh Data</button>
</header>

<main>
    <section class="grid" aria-label="Current Sensor Readings">
        <div class="card">
            <h2>Current Temperature</h2>
            <div class="value"><?php echo htmlspecialchars($latest['temperature']); ?> °C</div>
        </div>
        <div class="card">
            <h2>Current Humidity</h2>
            <div class="value"><?php echo htmlspecialchars($latest['humidity']); ?> %</div>
        </div>
        <div class="card">
            <h2>Last Updated</h2>
            <p style="font-weight: 500; margin-top: 15px;"><?php echo htmlspecialchars($latest['timestamp']); ?></p>
        </div>
    </section>

    <section class="chart-container" aria-label="Sensor Data Trends Graph">
        <h2>Environmental Trends (Last 10 Readings)</h2>
        <canvas id="telemetryChart" width="400" height="150"></canvas>
    </section>

    <section aria-label="Historical Telemetry Log Table">
        <h2>Recent Telemetry Log</h2>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($readings)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">Waiting for incoming data stream from background serial port...</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($readings as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                            <td><?php echo htmlspecialchars($row['temperature']); ?> °C</td>
                            <td><?php echo htmlspecialchars($row['humidity']); ?> %</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<script>
// Extract arrays from PHP data for ChartJS mapping
const timestamps = <?php echo json_encode(array_column($chartData, 'timestamp')); ?>;
const tempHistory = <?php echo json_encode(array_column($chartData, 'temperature')); ?>;
const humHistory = <?php echo json_encode(array_column($chartData, 'humidity')); ?>;

const ctx = document.getElementById('telemetryChart').getContext('2d');
const telemetryChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: timestamps.map(t => t.split(' ')[1]), // Extract just the time portion (HH:MM:SS)
        datasets: [
            {
                label: 'Temperature (°C)',
                data: tempHistory,
                borderColor: '#e63946',
                backgroundColor: 'rgba(230, 57, 70, 0.1)',
                tension: 0.3,
                yAxisID: 'y-temp'
            },
            {
                label: 'Humidity (%)',
                data: humHistory,
                borderColor: '#457b9d',
                backgroundColor: 'rgba(69, 123, 157, 0.1)',
                tension: 0.3,
                yAxisID: 'y-hum'
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            'y-temp': {
                type: 'linear',
                position: 'left',
                title: { display: true, text: 'Temperature (°C)' }
            },
            'y-hum': {
                type: 'linear',
                position: 'right',
                title: { display: true, text: 'Humidity (%)' },
                grid: { drawOnChartArea: false } // prevent overlapping grid lines
            }
        }
    }
});
</script>

</body>
</html>