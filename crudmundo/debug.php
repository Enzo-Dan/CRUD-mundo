<?php
session_start();
require 'config.php';

echo "<h2>🔍 DEBUG DO SISTEMA</h2>";

try {
    // Verificar tabela paises
    $paises = $db->query("SELECT * FROM paises")->fetchAll();
    echo "<h3>📊 Países na tabela:</h3>";
    if (empty($paises)) {
        echo "❌ Tabela PAISES está VAZIA<br>";
    } else {
        echo "✅ " . count($paises) . " países encontrados<br>";
        echo "<pre>";
        print_r($paises);
        echo "</pre>";
    }

    // Verificar estrutura da tabela
    $estrutura = $db->query("DESCRIBE paises")->fetchAll();
    echo "<h3>🏗️ Estrutura da tabela PAISES:</h3>";
    echo "<pre>";
    print_r($estrutura);
    echo "</pre>";

} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage();
}
?>