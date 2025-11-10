<?php

// Incluir configurações e funções
require 'config.php';
require 'functions.php';

// ============================================
// 🎬 PROCESSAMENTO DAS AÇÕES
// ============================================

// ADICIONAR PAÍS
if (isset($_POST['action']) && $_POST['action'] === 'add_country') {
    $nome = clean($_POST['nome'] ?? '');
    $continente = clean($_POST['continente'] ?? '');
    $populacao = (int)($_POST['populacao'] ?? 0);
    $idioma = clean($_POST['idioma'] ?? '');
    
    if ($nome && $continente && $populacao > 0 && $idioma) {
        $apiData = getCountryData($nome);
        
        $bandeira = $apiData['bandeira'] ?? null;
        $moeda = $apiData['moeda'] ?? 'Não disponível';
        $capital = $apiData['capital'] ?? 'Não informada';
        
        try {
            // CORREÇÃO: Usando os nomes corretos das colunas
            $stmt = $db->prepare("INSERT INTO paises (nome, continente, populacao, idioma, bandeira, moeda, capital) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$nome, $continente, $populacao, $idioma, $bandeira, $moeda, $capital])) {
                msg('ok', '✅ País adicionado com sucesso!');
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                msg('error', '❌ Este país já existe no sistema!');
            } else {
                msg('error', '❌ Erro ao adicionar país!');
            }
        }
    } else {
        msg('error', '❌ Preencha todos os campos corretamente!');
    }
    header('Location: ?');
    exit;
}

// ADICIONAR CIDADE
if (isset($_POST['action']) && $_POST['action'] === 'add_city') {
    $nome = clean($_POST['nome'] ?? '');
    $populacao = (int)($_POST['populacao'] ?? 0);
    $id_pais = (int)($_POST['id_pais'] ?? 0);
    
    if ($nome && $populacao > 0 && $id_pais > 0) {
        $weatherData = getWeather($nome, API_KEY);
        
        $temperatura = $weatherData['temperatura'] ?? null;
        $descricao_clima = $weatherData['clima'] ?? 'Dados não disponíveis';
        $umidade = $weatherData['umidade'] ?? null;
        $velocidade_vento = $weatherData['vento'] ?? null;
        
        try {
            // CORREÇÃO: Usando os nomes corretos das colunas da tabela cidades
            $stmt = $db->prepare("INSERT INTO cidades (nome, populacao, id_pais, temperatura, descricao_clima, umidade, velocidade_vento) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$nome, $populacao, $id_pais, $temperatura, $descricao_clima, $umidade, $velocidade_vento])) {
                msg('ok', '✅ Cidade adicionada com sucesso!');
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                msg('error', '❌ Esta cidade já existe neste país!');
            } else {
                msg('error', '❌ Erro ao adicionar cidade!');
            }
        }
    } else {
        msg('error', '❌ Preencha todos os campos corretamente!');
    }
    header('Location: ?');
    exit;
}

// EDITAR PAÍS
if (isset($_POST['action']) && $_POST['action'] === 'edit_country') {
    $id_pais = (int)($_POST['id'] ?? 0);
    $nome = clean($_POST['nome'] ?? '');
    $continente = clean($_POST['continente'] ?? '');
    $populacao = (int)($_POST['populacao'] ?? 0);
    $idioma = clean($_POST['idioma'] ?? '');
    
    if ($id_pais > 0 && $nome && $continente && $populacao > 0 && $idioma) {
        try {
            // CORREÇÃO: Usando id_pais
            $stmt = $db->prepare("UPDATE paises SET nome = ?, continente = ?, populacao = ?, idioma = ? WHERE id_pais = ?");
            
            if ($stmt->execute([$nome, $continente, $populacao, $idioma, $id_pais])) {
                msg('ok', '✅ País atualizado com sucesso!');
            }
        } catch (PDOException $e) {
            msg('error', '❌ Erro ao atualizar país!');
        }
    }
    header('Location: ?');
    exit;
}

// EDITAR CIDADE
if (isset($_POST['action']) && $_POST['action'] === 'edit_city') {
    $id_cidade = (int)($_POST['id'] ?? 0);
    $nome = clean($_POST['nome'] ?? '');
    $populacao = (int)($_POST['populacao'] ?? 0);
    
    if ($id_cidade > 0 && $nome && $populacao > 0) {
        $weatherData = getWeather($nome, API_KEY);
        
        $temperatura = $weatherData['temperatura'] ?? null;
        $descricao_clima = $weatherData['clima'] ?? 'Dados não disponíveis';
        $umidade = $weatherData['umidade'] ?? null;
        $velocidade_vento = $weatherData['vento'] ?? null;
        
        try {
            // CORREÇÃO: Usando id_cidade e nomes corretos das colunas
            $stmt = $db->prepare("UPDATE cidades SET nome = ?, populacao = ?, temperatura = ?, descricao_clima = ?, umidade = ?, velocidade_vento = ? WHERE id_cidade = ?");
            
            if ($stmt->execute([$nome, $populacao, $temperatura, $descricao_clima, $umidade, $velocidade_vento, $id_cidade])) {
                msg('ok', '✅ Cidade atualizada com sucesso!');
            }
        } catch (PDOException $e) {
            msg('error', '❌ Erro ao atualizar cidade!');
        }
    }
    header('Location: ?');
    exit;
}

// DELETAR PAÍS
if (isset($_POST['action']) && $_POST['action'] === 'delete_country') {
    $id_pais = (int)($_POST['id'] ?? 0);
    
    if ($id_pais > 0) {
        try {
            // CORREÇÃO: Usando id_pais
            $stmt = $db->prepare("DELETE FROM paises WHERE id_pais = ?");
            
            if ($stmt->execute([$id_pais])) {
                msg('ok', '✅ País deletado com sucesso!');
            }
        } catch (PDOException $e) {
            msg('error', '❌ Erro ao deletar país!');
        }
    }
    header('Location: ?');
    exit;
}

// DELETAR CIDADE
if (isset($_POST['action']) && $_POST['action'] === 'delete_city') {
    $id_cidade = (int)($_POST['id'] ?? 0);
    
    if ($id_cidade > 0) {
        try {
            // CORREÇÃO: Usando id_cidade
            $stmt = $db->prepare("DELETE FROM cidades WHERE id_cidade = ?");
            
            if ($stmt->execute([$id_cidade])) {
                msg('ok', '✅ Cidade deletada com sucesso!');
            }
        } catch (PDOException $e) {
            msg('error', '❌ Erro ao deletar cidade!');
        }
    }
    header('Location: ?');
    exit;
}

// DELETAR TODAS AS CIDADES DO PAÍS
if (isset($_POST['action']) && $_POST['action'] === 'delete_all_cities') {
    $id_pais = (int)($_POST['id_pais'] ?? 0);
    
    if ($id_pais > 0) {
        try {
            $stmt = $db->prepare("DELETE FROM cidades WHERE id_pais = ?");
            
            if ($stmt->execute([$id_pais])) {
                msg('ok', '✅ Todas as cidades do país foram deletadas!');
            }
        } catch (PDOException $e) {
            msg('error', '❌ Erro ao deletar cidades!');
        }
    }
    header('Location: ?');
    exit;
}

// API - OBTER DADOS DO PAÍS
if (isset($_GET['action']) && $_GET['action'] === 'get_country') {
    header('Content-Type: application/json');
    
    $id_pais = (int)($_GET['id'] ?? 0);
    
    try {
        // CORREÇÃO: Usando id_pais
        $stmt = $db->prepare("SELECT * FROM paises WHERE id_pais = ?");
        $stmt->execute([$id_pais]);
        $pais = $stmt->fetch();
        
        echo json_encode([
            'success' => !empty($pais),
            'pais' => $pais
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// API - OBTER DADOS DA CIDADE
if (isset($_GET['action']) && $_GET['action'] === 'get_city') {
    header('Content-Type: application/json');
    
    $id_cidade = (int)($_GET['id'] ?? 0);
    
    try {
        // CORREÇÃO: Usando id_cidade
        $stmt = $db->prepare("SELECT * FROM cidades WHERE id_cidade = ?");
        $stmt->execute([$id_cidade]);
        $cidade = $stmt->fetch();
        
        echo json_encode([
            'success' => !empty($cidade),
            'cidade' => $cidade
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// ============================================
// 📊 CARREGAR DADOS PARA EXIBIÇÃO
// ============================================

try {
    // Carregar países
    $paises = $db->query("SELECT * FROM paises ORDER BY nome")->fetchAll();
    
    // Carregar cidades organizadas por país
    $cidades = [];
    foreach ($paises as $pais) {
        // CORREÇÃO: Usando id_pais
        if (isset($pais['id_pais']) && !empty($pais['id_pais'])) {
            $pais_id = $pais['id_pais'];
            $stmt = $db->prepare("SELECT * FROM cidades WHERE id_pais = ? ORDER BY nome");
            $stmt->execute([$pais_id]);
            $cidades[$pais_id] = $stmt->fetchAll();
        }
    }
} catch (PDOException $e) {
    // Se der erro, mostra mensagem mas não para a execução
    $paises = [];
    $cidades = [];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌍 CRUD Mundo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>🌍 CRUD Mundo</h1>
    <p>Gerenciamento Completo de Países e Cidades</p>
</header>

<div class="container">
    <?php showMsg(); ?>
    
    <button class="btn-add" onclick="openModal('addCountryModal')">+ Adicionar Novo País</button>
    
    <?php if (empty($paises)): ?>
        <div class="empty">
            <h3>📍 Nenhum país cadastrado</h3>
            <p>Comece adicionando o primeiro país ao sistema!</p>
        </div>
    <?php else: ?>
        <div class="countries-grid">
            <?php foreach ($paises as $pais): 
                // CORREÇÃO: Usando id_pais
                $pais_id = isset($pais['id_pais']) ? $pais['id_pais'] : 0;
                $cidadesPais = isset($cidades[$pais_id]) ? $cidades[$pais_id] : [];
                
                // Se não tem ID válido, pula este país
                if ($pais_id === 0) {
                    continue;
                }
            ?>
                <div class="country-card">
                    <div class="country-header">
                        <div class="country-title">
                            <?php if (!empty($pais['bandeira'])): ?>
                                <img src="<?= htmlspecialchars($pais['bandeira']) ?>" alt="Bandeira" class="country-flag">
                            <?php endif; ?>
                            <div class="country-name"><?= htmlspecialchars($pais['nome']) ?></div>
                        </div>
                        <div class="country-info">
                            <strong>Continente:</strong> <?= htmlspecialchars($pais['continente']) ?><br>
                            <strong>População:</strong> <?= fmt($pais['populacao']) ?><br>
                            <strong>Idioma:</strong> <?= htmlspecialchars($pais['idioma']) ?><br>
                            <?php if (!empty($pais['capital'])): ?>
                                <strong>Capital:</strong> <?= htmlspecialchars($pais['capital']) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($pais['moeda'])): ?>
                                <strong>Moeda:</strong> <?= htmlspecialchars($pais['moeda']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="country-body">
                        <div class="cities-title">
                            Cidades
                            <span class="cities-count"><?= count($cidadesPais) ?></span>
                        </div>
                        
                        <?php if (!empty($cidadesPais)): ?>
                            <div class="cities-list">
                                <?php foreach ($cidadesPais as $cidade): ?>
                                    <div class="city-item">
                                        <div class="city-name">📍 <?= htmlspecialchars($cidade['nome']) ?></div>
                                        <div class="city-details">👥 População: <?= fmt($cidade['populacao']) ?></div>
                                        <?php if (!empty($cidade['temperatura'])): ?>
                                            <div class="city-weather">
                                                🌡️ <?= $cidade['temperatura'] ?>°C | 
                                                ☁️ <?= htmlspecialchars($cidade['descricao_clima']) ?> | 
                                                💧 <?= $cidade['umidade'] ?>% | 
                                                💨 <?= $cidade['velocidade_vento'] ?> m/s
                                            </div>
                                        <?php endif; ?>
                                        <div class="city-actions">
                                            <button class="btn-primary" onclick="editCity(<?= $cidade['id_cidade'] ?>)">✏️ Editar</button>
                                            <button class="btn-danger" onclick="confirmDelete('city', <?= $cidade['id_cidade'] ?>, '<?= htmlspecialchars(addslashes($cidade['nome'])) ?>')">🗑️ Deletar</button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-cities">Nenhuma cidade cadastrada</div>
                        <?php endif; ?>
                        
                        <button class="btn-add-city" onclick="openModal('addCityModal<?= $pais_id ?>')">+ Adicionar Cidade</button>
                        
                        <?php if (!empty($cidadesPais)): ?>
                            <button class="btn-delete-all" onclick="confirmDelete('all_cities', <?= $pais_id ?>, 'todas as cidades de <?= htmlspecialchars(addslashes($pais['nome'])) ?>')">🗑️ Deletar Todas</button>
                        <?php endif; ?>
                        
                        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
                            <button class="btn-primary" style="flex: 1;" onclick="editCountry(<?= $pais_id ?>)">✏️ Editar País</button>
                            <button class="btn-danger" style="flex: 1;" onclick="confirmDelete('country', <?= $pais_id ?>, '<?= htmlspecialchars(addslashes($pais['nome'])) ?>')">🗑️ Deletar País</button>
                        </div>
                    </div>
                </div>
                
                <!-- MODAL ADICIONAR CIDADE -->
                <div class="modal" id="addCityModal<?= $pais_id ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2>Adicionar Cidade em <?= htmlspecialchars($pais['nome']) ?></h2>
                            <button class="modal-close" onclick="closeModal('addCityModal<?= $pais_id ?>')">✕</button>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_city">
                            <input type="hidden" name="id_pais" value="<?= $pais_id ?>">
                            
                            <div class="form-group">
                                <label>Nome da Cidade</label>
                                <input type="text" name="nome" required>
                            </div>
                            
                            <div class="form-group">
                                <label>População</label>
                                <input type="number" name="populacao" min="1" required>
                            </div>
                            
                            <div class="form-actions">
                                <button type="button" class="btn-primary" onclick="closeModal('addCityModal<?= $pais_id ?>')">Cancelar</button>
                                <button type="submit" class="btn-secondary">Adicionar</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL ADICIONAR PAÍS -->
<div class="modal" id="addCountryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Adicionar País</h2>
            <button class="modal-close" onclick="closeModal('addCountryModal')">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_country">
            
            <div class="form-group">
                <label>Nome do País</label>
                <input type="text" name="nome" required>
            </div>
            
            <div class="form-group">
                <label>Continente</label>
                <select name="continente" required>
                    <option value="">Selecione...</option>
                    <option>América do Norte</option>
                    <option>América do Sul</option>
                    <option>Europa</option>
                    <option>Ásia</option>
                    <option>África</option>
                    <option>Oceania</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>População</label>
                <input type="number" name="populacao" min="1" required>
            </div>
            
            <div class="form-group">
                <label>Idioma Principal</label>
                <input type="text" name="idioma" required>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="closeModal('addCountryModal')">Cancelar</button>
                <button type="submit" class="btn-secondary">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR PAÍS -->
<div class="modal" id="editCountryModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar País</h2>
            <button class="modal-close" onclick="closeModal('editCountryModal')">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit_country">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-group">
                <label>Nome do País</label>
                <input type="text" name="nome" id="edit_nome" required>
            </div>
            
            <div class="form-group">
                <label>Continente</label>
                <select name="continente" id="edit_continente" required>
                    <option>América do Norte</option>
                    <option>América do Sul</option>
                    <option>Europa</option>
                    <option>Ásia</option>
                    <option>África</option>
                    <option>Oceania</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>População</label>
                <input type="number" name="populacao" id="edit_populacao" min="1" required>
            </div>
            
            <div class="form-group">
                <label>Idioma Principal</label>
                <input type="text" name="idioma" id="edit_idioma" required>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="closeModal('editCountryModal')">Cancelar</button>
                <button type="submit" class="btn-secondary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR CIDADE -->
<div class="modal" id="editCityModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Editar Cidade</h2>
            <button class="modal-close" onclick="closeModal('editCityModal')">✕</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="edit_city">
            <input type="hidden" name="id" id="edit_city_id">
            
            <div class="form-group">
                <label>Nome da Cidade</label>
                <input type="text" name="nome" id="edit_city_nome" required>
            </div>
            
            <div class="form-group">
                <label>População</label>
                <input type="number" name="populacao" id="edit_city_populacao" min="1" required>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="closeModal('editCityModal')">Cancelar</button>
                <button type="submit" class="btn-secondary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="script.js"></script>

</body>
</html>