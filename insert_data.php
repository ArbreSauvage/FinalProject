<?php
// insert_data.php
include 'db.php';

$portName = 'COM7'; // Make sure this matches your device's COM port!
$baudRate = 9600;   // Matches the microcontroller's baud rate

echo "Configuring serial port {$portName}...\n";

// Use Windows mode command to configure the COM port parameters natively
exec("mode {$portName} baud={$baudRate} data=8 stop=1 parity=n xon=on");

echo "Listening to serial port {$portName} and saving to database... (Press Ctrl+C to stop)\n";

// Open the COM port as a regular file stream for reading
$serialHandle = fopen($portName, "r");

if (!$serialHandle) {
    die("Error: Could not open Serial port {$portName}. Ensure the board is plugged in and the Energia Serial Monitor is CLOSED.");
}

// Variables to hold matching pairs
$currentTemperature = null;
$currentHumidity = null;

while (!feof($serialHandle)) {
    // Read a line from the serial port buffer
    $line = fgets($serialHandle);

    if ($line !== false) {
        $line = trim($line);

        // Parse incoming strings (matching the output formats from main.ino) [cite: 59]
        if (preg_match('/Humidit[é]s*:\s*([0-9.]+)/i', $line, $matchesH)) {
            $currentHumidity = $matchesH[1];
        }
        if (preg_match('/Temp[é]ratures*:\s*([0-9.]+)/i', $line, $matchesT)) {
            $currentTemperature = $matchesT[1];
        }

        // Once both metrics are extracted, commit them to the shared database table
        if ($currentTemperature !== null && $currentHumidity !== null) {
            try {
                $stmt = $pdo->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (:temp, :hum)");
                $stmt->execute([
                    ':temp' => $currentTemperature,
                    ':hum'  => $currentHumidity
                ]);
                echo "[" . date('Y-m-d H:i:s') . "] Data saved: Temp = {$currentTemperature}°C, Hum = {$currentHumidity}%\n";
            } catch (PDOException $e) {
                echo "Database Error: " . $e->getMessage() . "\n";
            }

            // Clear cache tracking variables for next iteration cycle
            $currentTemperature = null;
            $currentHumidity = null;
        }
    }

    // Tiny sleep to avoid pegging the CPU core at 100% usage
    usleep(100000);
}

fclose($serialHandle);
?>