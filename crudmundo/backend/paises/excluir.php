<?php
include_once '../conexao.php';

if ($_POST && isset($_POST['id'])) {
    $id = $_POST['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Verificar se o país tem cidades associadas
        $query_check = "SELECT COUNT(*) as total FROM cidades WHERE id_pais = :id";
        $stmt_check = $db->prepare($query_check);
        $stmt_check->bindParam(":id", $id);
        $stmt_check->execute();
        $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total'] > 0) {
            echo json_encode([
                "success" => false,
                "message" => "Não é possível excluir o país pois existem cidades associadas a ele."
            ]);
            return;
        }
        
        // Excluir país
        $query = "DELETE FROM paises WHERE id_pais = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "País excluído com sucesso!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Erro ao excluir país."
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