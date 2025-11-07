<?php
include_once '../conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $query = "SELECT * FROM paises WHERE id_pais = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $pais = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($pais);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "País não encontrado."
            ]);
        }
        
    } catch(PDOException $exception) {
        echo json_encode([
            "success" => false,
            "message" => "Erro ao carregar país: " . $exception->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "ID não fornecido."
    ]);
}
?>