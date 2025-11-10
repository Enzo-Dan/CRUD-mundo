<?php
// 🗄️ CONFIGURAÇÕES DO SISTEMA
define('API_KEY', '3eb984244e30f1f2f3d7153993f591c0'); // Adicione sua chave da OpenWeather

// CONFIGURAÇÕES DO BANCO DE DADOS
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bd_mundo');

// CONEXÃO COM BANCO DE DADOS
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("❌ Erro de conexão com o banco de dados: " . $e->getMessage());
}

// Iniciar sessão
session_start();
?>