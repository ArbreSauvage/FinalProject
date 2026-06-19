<?php
// insert_data.php
include 'db.php';

$portName = 'COM7';
$baudRate = 9600;

echo "Configuring serial port {$portName}...\n";
// Configuration native Windows
exec("mode {$portName} baud={$baudRate} data=8 stop=1 parity=n xon=on");

echo "Listening to serial port {$portName} and saving to database... (Press Ctrl+C to stop)\n";

// CORRECTION : Ouverture en mode binaire 'rb' avec le chemin réseau Windows pour éviter les conflits
$serialHandle = fopen("\\\\.\\" . $portName, "rb");

if (!$serialHandle) {
    die("Error: Could not open Serial port {$portName}. Ensure Putty is CLOSED.");
}

// CORRECTION CRUCIALE : Désactive le blocage de flux pour forcer PHP à lire les caractères en temps réel
stream_set_blocking($serialHandle, false);

$buffer = "";
$currentTemperature = null;
$currentHumidity = null;

while (true) {
    // Lit ce qui arrive sur le port (jusqu'à 128 caractères d'un coup)
    $chunk = fread($serialHandle, 128);

    if ($chunk !== false && $chunk !== "") {
        $buffer .= $chunk;

        // Si on détecte un retour à la ligne envoyé par la Tiva
        if (strpos($buffer, "\n") !== false) {
            $lines = explode("\n", $buffer);
            // On garde le morceau de ligne incomplet pour le prochain cycle
            $buffer = array_pop($lines);

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Affiche le texte brut reçu (comme Putty) pour que tu puisses valider le flux !
                echo "[TIVA RAW]: " . $line . "\n";

                // Extraction via tes expressions régulières d'origine
                if (preg_match('/Humidit[é]s*:\s*([0-9.]+)/i', $line, $matchesH)) {
                    $currentHumidity = $matchesH[1];
                }
                if (preg_match('/Temp[é]ratures*:\s*([0-9.]+)/i', $line, $matchesT)) {
                    $currentTemperature = $matchesT[1];
                }

                // Dès que la paire est complète, on insère dans Hangar
                if ($currentTemperature !== null && $currentHumidity !== null) {
                    try {
                        // Ajout de 'sensor_type' requis par ton schéma de BDD SQL
                        $stmt = $pdo->prepare("INSERT INTO sensor_data (sensor_type, temperature, humidity) VALUES (:type, :temp, :hum)");
                        $stmt->execute([
                            ':type' => 'DHT11',
                            ':temp' => $currentTemperature,
                            ':hum'  => $currentHumidity
                        ]);
                        echo "[" . date('Y-m-d H:i:s') . "] ✅ Enregistré sur Hangar : Temp = {$currentTemperature}°C, Hum = {$currentHumidity}%\n";
                    } catch (PDOException $e) {
                        echo "Database Error: " . $e->getMessage() . "\n";
                    }

                    // Reset pour la prochaine capture
                    $currentTemperature = null;
                    $currentHumidity = null;
                }
            }
        }
    }

    // Pause de 100ms pour pas faire surchauffer le processeur
    usleep(100000);
}

fclose($serialHandle);
?>