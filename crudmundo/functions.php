<?php
// ============================================
// 🛠️ FUNÇÕES AUXILIARES
// ============================================

/**
 * Limpa e sanitiza dados de entrada
 */
function clean($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formata números com separadores
 */
function fmt($n) {
    return number_format($n ?? 0, 0, ',', '.');
}

/**
 * Busca dados do país na API REST Countries
 */
function getCountryData($name) {
    $url = "https://restcountries.com/v3.1/name/" . urlencode($name);
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if (!$response) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE || !isset($data[0])) {
        return null;
    }
    
    $country = $data[0];
    
    // Extrai moeda
    $moeda = 'Não informado';
    if (isset($country['currencies'])) {
        $currencyData = reset($country['currencies']);
        $moeda = $currencyData['name'] ?? array_key_first($country['currencies']);
    }
    
    return [
        'bandeira' => $country['flags']['svg'] ?? $country['flags']['png'] ?? null,
        'moeda' => $moeda,
        'capital' => $country['capital'][0] ?? 'Não informada'
    ];
}

/**
 * Busca dados do clima na OpenWeather API
 */
function getWeather($city, $apiKey) {
    if (empty($apiKey)) {
        return null;
    }
    
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) 
         . "&appid=" . urlencode($apiKey) . "&units=metric&lang=pt_br";
    
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if (!$response) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    if (isset($data['cod']) && $data['cod'] != 200) {
        return null;
    }
    
    if (!isset($data['main'])) {
        return null;
    }
    
    return [
        'temperatura' => round($data['main']['temp'], 1),
        'clima' => $data['weather'][0]['description'] ?? 'Não disponível',
        'umidade' => $data['main']['humidity'] ?? 'N/A',
        'vento' => round($data['wind']['speed'] ?? 0, 1)
    ];
}

/**
 * Define mensagem de feedback
 */
function msg($type, $text) {
    $_SESSION['msg'] = ['type' => $type, 'text' => $text];
}

/**
 * Exibe mensagem de feedback
 */
function showMsg() {
    if (!isset($_SESSION['msg'])) {
        return;
    }
    
    $message = $_SESSION['msg'];
    $bgColor = $message['type'] === 'ok' ? '#4cc3c7' : '#ff1e42';
    
    echo "<div class='alert' style='background: {$bgColor}; color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;'>
            <span>{$message['text']}</span>
            <button onclick='this.parentElement.style.display=\"none\"' style='background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer;'>✕</button>
          </div>";
    
    unset($_SESSION['msg']);
}
?>