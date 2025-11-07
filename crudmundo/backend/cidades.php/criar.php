<?php
include_once '../conexao.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Receber e sanitizar dados
    $nome = sanitize($_POST['nome']);
    $populacao = sanitize($_POST['populacao']);
    $id_pais = sanitize($_POST['id_pais']);
    $latitude = isset($_POST['latitude']) ? sanitize($_POST['latitude']) : null;
    $longitude = isset($_POST['longitude']) ? sanitize($_POST['longitude']) : null;
    
    try {
        // Verificar se o país existe
        $query_check = "SELECT id_pais FROM paises WHERE id_pais = :id_pais";
        $stmt_check = $db->prepare($query_check);
        $stmt_check->bindParam(":id_pais", $id_pais);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() == 0) {
            echo json_encode([
                "success" => false,
                "message" => "País não encontrado."
            ]);
            return;
        }
        
        // Inserir nova cidade
        $query = "INSERT INTO cidades 
                 (nome, populacao, id_pais, latitude, longitude) 
                 VALUES 
                 (:nome, :populacao, :id_pais, :latitude, :longitude)";
        
        $stmt = $db->prepare($query);
        
        // Bind dos parâmetros
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":populacao", $populacao);
        $stmt->bindParam(":id_pais", $id_pais);
        $stmt->bindParam(":latitude", $latitude);
        $stmt->bindParam(":longitude", $longitude);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Cidade criada com sucesso!",
                "id" => $db->lastInsertId()
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Erro ao criar cidade."
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
        "message" => "Método não permitido."
    ]);
}
?>