<?php
include_once '../conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $query = "SELECT * FROM cidades WHERE id_cidade = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $cidade = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($cidade);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Cidade não encontrada."
            ]);
        }
        
    } catch(PDOException $exception) {
        echo json_encode([
            "success" => false,
            "message" => "Erro ao carregar cidade: " . $exception->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID não fornecido."
    ]);
}
?>