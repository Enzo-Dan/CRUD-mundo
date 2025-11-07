<?php
include_once '../conexao.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Receber e sanitizar dados
    $nome = sanitize($_POST['nome']);
    $nome_oficial = sanitize($_POST['nome_oficial']);
    $continente = sanitize($_POST['continente']);
    $populacao = sanitize($_POST['populacao']);
    $idioma = sanitize($_POST['idioma']);
    $moeda = isset($_POST['moeda']) ? sanitize($_POST['moeda']) : null;
    $capital = isset($_POST['capital']) ? sanitize($_POST['capital']) : null;
    
    try {
        // Inserir novo país
        $query = "INSERT INTO paises 
                 (nome, nome_oficial, continente, populacao, idioma, moeda, capital) 
                 VALUES 
                 (:nome, :nome_oficial, :continente, :populacao, :idioma, :moeda, :capital)";
        
        $stmt = $db->prepare($query);
        
        // Bind dos parâmetros
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":nome_oficial", $nome_oficial);
        $stmt->bindParam(":continente", $continente);
        $stmt->bindParam(":populacao", $populacao);
        $stmt->bindParam(":idioma", $idioma);
        $stmt->bindParam(":moeda", $moeda);
        $stmt->bindParam(":capital", $capital);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "País criado com sucesso!",
                "id" => $db->lastInsertId()
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Erro ao criar país."
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