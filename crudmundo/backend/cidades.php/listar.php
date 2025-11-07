<?php
include_once '../conexao.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT c.*, p.nome as nome_pais 
              FROM cidades c 
              LEFT JOIN paises p ON c.id_pais = p.id_pais 
              ORDER BY c.nome";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $cidades = [];
    
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cidades[] = [
                "id_cidade" => $row['id_cidade'],
                "nome" => $row['nome'],
                "populacao" => $row['populacao'],
                "id_pais" => $row['id_pais'],
                "nome_pais" => $row['nome_pais'],
                "latitude" => $row['latitude'],
                "longitude" => $row['longitude']
            ];
        }
    }
    
    echo json_encode($cidades);
    
} catch(PDOException $exception) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao carregar cidades: " . $exception->getMessage()
    ]);
}
?>