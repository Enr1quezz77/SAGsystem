<?php
$file = 'modelo/config_biometrico.json';
if (!file_exists($file)) {
    $data = [
        "ip" => "192.168.1.201",
        "puerto" => "4370"
    ];
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    echo "Created";
} else {
    echo "Exists";
}
?>
