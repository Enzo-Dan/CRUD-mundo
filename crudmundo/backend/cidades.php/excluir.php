<?php
include_once '../conexao.php';

if ($_POST && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Excluir cidade
        $query = "DELETE FROM cidades WHERE id_cidade = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Cidade excluída com sucesso!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Erro ao excluir cidade."
            ]);
        }
        
    } catch(PDOException $exception) {
        echo json_encode([
            "success" => false,
            "message" => "Erro de banco de dados: " . $exception->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID não fornecido."
    ]);
}
?>