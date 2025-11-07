<?php
include_once '../conexao.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT * FROM paises ORDER BY nome";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $paises = [];
    
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paises[] = [
                "id_pais" => $row['id_pais'],
                "nome" => $row['nome'],
                "nome_oficial" => $row['nome_oficial'],
                "continente" => $row['continente'],
                "populacao" => $row['populacao'],
                "idioma" => $row['idioma'],
                "moeda" => $row['moeda'],
                "capital" => $row['capital'],
                "bandeira_url" => $row['bandeira_url']
            ];
        }
    }
    
    echo json_encode($paises);
    
} catch(PDOException $exception) {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao carregar países: " . $exception->getMessage()
    ]);
}
?>