<?php
include_once '../conexao.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Receber e sanitizar dados
    $id_pais = sanitize($_POST['id_pais']);
    $nome = sanitize($_POST['nome']);
    $nome_oficial = sanitize($_POST['nome_oficial']);
    $continente = sanitize($_POST['continente']);
    $populacao = sanitize($_POST['populacao']);
    $idioma = sanitize($_POST['idioma']);
    $moeda = isset($_POST['moeda']) ? sanitize($_POST['moeda']) : null;
    $capital = isset($_POST['capital']) ? sanitize($_POST['capital']) : null;
    
    try {
        // Atualizar país
        $query = "UPDATE paises SET 
                 nome = :nome,
                 nome_oficial = :nome_oficial,
                 continente = :continente,
                 populacao = :populacao,
                 idioma = :idioma,
                 moeda = :moeda,
                 capital = :capital
                 WHERE id_pais = :id_pais";
        
        $stmt = $db->prepare($query);
        
        // Bind dos parâmetros
        $stmt->bindParam(":id_pais", $id_pais);
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
                "message" => "País atualizado com sucesso!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Erro ao atualizar país."
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