<?php
include_once 'conexao.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Estatísticas gerais
    $query_paises = "SELECT COUNT(*) as total_paises FROM paises";
    $query_cidades = "SELECT COUNT(*) as total_cidades FROM cidades";
    $query_populacao = "SELECT SUM(populacao) as populacao_total FROM paises";
    
    $stmt_paises = $db->prepare($query_paises);
    $stmt_cidades = $db->prepare($query_cidades);
    $stmt_populacao = $db->prepare($query_populacao);
    
    $stmt_paises->execute();
    $stmt_cidades->execute();
    $stmt_populacao->execute();
    
    $total_paises = $stmt_paises->fetch(PDO::FETCH_ASSOC)['total_paises'];
    $total_cidades = $stmt_cidades->fetch(PDO::FETCH_ASSOC)['total_cidades'];
    $populacao_total = $stmt_populacao->fetch(PDO::FETCH_ASSOC)['populacao_total'];
    
    // Estatísticas por continente
    $query_continentes = "
        SELECT 
            continente,
            COUNT(*) as total_paises,
            SUM(populacao) as populacao_total,
            (SELECT COUNT(*) FROM cidades c 
             INNER JOIN paises p ON c.id_pais = p.id_pais 
             WHERE p.continente = paises.continente) as total_cidades
        FROM paises 
        GROUP BY continente
        ORDER BY populacao_total DESC
    ";
    
    $stmt_continentes = $db->prepare($query_continentes);
    $stmt_continentes->execute();
    
    $estatisticas_continentes = [];
    while ($row = $stmt_continentes->fetch(PDO::FETCH_ASSOC)) {
        $estatisticas_continentes[] = $row;
    }
    
    // Cidade mais populosa
    $query_cidade_mais_populosa = "
        SELECT c.nome as cidade, p.nome as pais, c.populacao 
        FROM cidades c 
        INNER JOIN paises p ON c.id_pais = p.id_pais 
        ORDER BY c.populacao DESC 
        LIMIT 1
    ";
    
    $stmt_cidade_populosa = $db->prepare($query_cidade_mais_populosa);
    $stmt_cidade_populosa->execute();
    $cidade_mais_populosa = $stmt_cidade_populosa->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        "success" => true,
        "estatisticas_gerais" => [
            "total_paises" => $total_paises,
            "total_cidades" => $total_cidades,
            "populacao_total" => $populacao_total
        ],
        "estatisticas_continentes" => $estatisticas_continentes,
        "cidade_mais_populosa" => $cidade_mais_populosa
    ]);
    
} catch(PDOException $exception) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao carregar estatísticas: " . $exception->getMessage()
    ]);
}
?>