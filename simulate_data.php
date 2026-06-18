<?php
// simulate_data.php
include 'db.php';

echo "========================================================\n";
echo "    ISEP JOINT PROJECT - DHT11 HARDWARE SIMULATOR       \n";
echo "========================================================\n";
echo "Simulating live sensor data stream... (Press Ctrl+C to stop)\n\n";

// Baseline realistic indoor values
$baseTemperature = 22.0;
$baseHumidity = 50.0;

while (true) {
    // Generate small random fluctuations to look natural on the chart
    $temperatureFluche = (rand(-5, 5) / 10); // fluctuates by +/- 0.5°C
    $humidityFluche = rand(-2, 2);           // fluctuates by +/- 2%
    
    $currentTemperature = round($baseTemperature + $temperatureFluche, 1);
    $currentHumidity = max(0, min(100, $baseHumidity + $humidityFluche)); // Keep between 0-100%

    try {
        // Insert the fake hardware data into your real database table
        $stmt = $pdo->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (:temp, :hum)");
        $stmt->execute([
            ':temp' => $currentTemperature,
            ':hum'  => $currentHumidity
        ]);
        
        echo "[" . date('H:i:s') . "] [SIMULATED HW] Broadcast sent -> Temp: {$currentTemperature}°C | Hum: {$currentHumidity}%\n";
        
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage() . "\n";
    }

    // Wait 2 seconds before sending the next telemetry reading, exactly like a DHT11 [cite: 63]
    sleep(2); 
}
?>