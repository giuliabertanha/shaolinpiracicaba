<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

if (!isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

//Formatando o nome da modalidade e criar um nome de tabela válido
function formatar_nome_tabela($nome, $conn) {
    $nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    $nome_minusculo = strtolower($nome_sem_acentos);
    $nome_tabela = preg_replace('/[^a-z0-9_]+/', '_', $nome_minusculo);
    $nome_tabela = trim($nome_tabela, '_');
    return mysqli_real_escape_string($conn, $nome_tabela);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_modalidade_post = $_POST['id'] ?? null;

    //DELETE
    if (isset($_POST['excluir']) && !empty($id_modalidade_post)) {
        //Busca o nome da modalidade para saber qual tabela apagar
        $stmt_get_nome = $conn->prepare("SELECT nome FROM modalidades WHERE id = ?");
        $stmt_get_nome->bind_param("i", $id_modalidade_post);
        $stmt_get_nome->execute();
        $nome_modalidade = $stmt_get_nome->get_result()->fetch_assoc()['nome'];
        $stmt_get_nome->close();

        //Deleta o registro da modalidade
        $stmt = $conn->prepare("DELETE FROM modalidades WHERE id = ?");
        $stmt->bind_param("i", $id_modalidade_post);
        if ($stmt->execute()) {
            //Se teve sucesso, apaga a tabela correspondente
            $nome_tabela = formatar_nome_tabela($nome_modalidade, $conn);
            $conn->query("DROP TABLE IF EXISTS `$nome_tabela`");
            echo "<script>alert('Modalidade e todos os registros de alunos vinculados foram excluídos com sucesso!'); window.location.href = 'cadastro_modalidades.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir modalidade: " . $stmt->error . "'); window.location.href = 'cadastro_modalidades.php';</script>";
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    //UPDATE
    $nome = $_POST['nome'];
    $professores_selecionados = $_POST['professores'] ?? [];
    $faixas = $_POST['faixas'] ?? [];

    if (count($professores_selecionados) >= 4) {
        echo "<script>alert('Você só pode selecionar no máximo 4 professores.'); window.history.back();</script>";
        exit;
    }

    $id_professor1 = isset($professores_selecionados[0]) ? (int)$professores_selecionados[0] : null;
    $id_professor2 = isset($professores_selecionados[1]) ? (int)$professores_selecionados[1] : null;
    $id_professor3 = isset($professores_selecionados[2]) ? (int)$professores_selecionados[2] : null;
    $id_professor4 = isset($professores_selecionados[3]) ? (int)$professores_selecionados[3] : null;

    if (!empty($id_modalidade_post)) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE modalidades SET nome = ?, id_professor1 = ?, id_professor2 = ?, id_professor3 = ?, id_professor4 = ? WHERE id = ?");
        $stmt->bind_param("siiiii", $nome, $id_professor1, $id_professor2, $id_professor3, $id_professor4, $id_modalidade_post);
    } 

    if ($stmt->execute()) {
        $novo_id = !empty($id_modalidade_post) ? $id_modalidade_post : $conn->insert_id;

        // Atualizar faixas/graduações
        if (!empty($id_modalidade_post)) {
            // 1. Deletar graduações antigas
            $stmt_delete_faixas = $conn->prepare("DELETE FROM graduacoes WHERE id_modalidade = ?");
            $stmt_delete_faixas->bind_param("i", $id_modalidade_post);
            $stmt_delete_faixas->execute();
            $stmt_delete_faixas->close();

            // 2. Inserir as novas graduações
            $stmt_faixa = $conn->prepare("INSERT INTO graduacoes (id_modalidade, nome, ordem) VALUES (?, ?, ?)");
            $ordem = 1;
            foreach ($faixas as $faixa_nome) {
                if (!empty(trim($faixa_nome))) {
                    $stmt_faixa->bind_param("isi", $id_modalidade_post, $faixa_nome, $ordem);
                    $stmt_faixa->execute();
                    $ordem++;
                }
            }
            $stmt_faixa->close();
        }

        echo "<script>alert('Dados salvos com sucesso!'); window.location.href = 'cadastro_modalidade.php?id=" . $novo_id . "';</script>";
    } else {
        echo "<script>alert('Erro ao salvar dados: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
    exit;
}

$id_modalidade = null;
$modalidade = [
    'nome' => '',
    'id_professor1' => null,
    'id_professor2' => null,
    'id_professor3' => null,
    'id_professor4' => null,
];
$faixas_cadastradas = [];

$sql_professores = "SELECT id, nome FROM usuarios WHERE tipo = 'P' ORDER BY nome";
$result_professores = $conn->query($sql_professores);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_modalidade = $_GET['id'];
    $titulo_pagina = "Editar modalidade";

    $stmt = $conn->prepare("SELECT id, nome, id_professor1, id_professor2, id_professor3, id_professor4 FROM modalidades WHERE id = ?");
    $stmt->bind_param("i", $id_modalidade);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $modalidade = $result->fetch_assoc();
    } else {
        echo "<script>alert('Modalidade não encontrada!'); window.location.href = 'cadastro_modalidades.php';</script>";
        exit;
    }
    $stmt->close();

    // Buscar as graduações existentes para esta modalidade
    $stmt_faixas = $conn->prepare("SELECT nome FROM graduacoes WHERE id_modalidade = ? ORDER BY ordem");
    $stmt_faixas->bind_param("i", $id_modalidade);
    $stmt_faixas->execute();
    $result_faixas = $stmt_faixas->get_result();
    while ($faixa = $result_faixas->fetch_assoc()) {
        $faixas_cadastradas[] = $faixa['nome'];
    }
    $stmt_faixas->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="img/icon.png" type="image/x-icon">
    <title>Shaolin Piracicaba | Cadastro de Modalidade</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg p-0">
            <div class="container-fluid">
                <div class="d-flex justify-content-between">
                    <a class="navbar-brand" href="index.html">
                        <img class="m-2" id="logo_cabecalho" src="img/logo.svg" alt="Logotipo">
                    </a>
                    <div class="flex-column">
                        <a href="index.html">
                            <h2 class="text-uppercase ms-2"><b id="titulo_cabecalho">Shaolin Kung Fu Piracicaba</b></h2>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <button class="btn-close d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close"></button>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.html">Home</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-bs-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Modalidades
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="modalidades.html">Visão Geral</a></li>
                                        <li><a class="dropdown-item" href="shaolin.html">Shaolin do Norte</a></li>
                                        <li><a class="dropdown-item" href="kids.html">Shaolin Kids</a></li>
                                        <li><a class="dropdown-item" href="sanda.html">Sanda</a></li>
                                        <li><a class="dropdown-item" href="taichi.html">Tai Chi Chuan</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="sobre.html">Sobre</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="premiacoes.html">Premiações</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="login.php">Área do Aluno/Professor</a>
                                </li>
                                <div id="user" class="d-flex align-items-center">
                                    <a href="#"><i class="fa-solid fa-user m-2" style="color: #161616;"></i></a>
                                    <span class="text-uppercase"><a href="#">Nome do usuário</a></span>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex flex-column align-items-center">
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Cadastro da modalidade</b></h2>  
        <form class="w-75" action="cadastro_modalidade.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_modalidade); ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" id="nome" value="<?php echo htmlspecialchars($modalidade['nome']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="professores" class="form-label">Professores</label>  
                <?php
                    if ($result_professores->num_rows > 0) {
                        $result_professores->data_seek(0); 
                        while($professor = $result_professores->fetch_assoc()) {
                            $checked = '';
                            if ($modalidade['id_professor1'] == $professor['id'] || $modalidade['id_professor2'] == $professor['id'] || $modalidade['id_professor3'] == $professor['id'] || $modalidade['id_professor4'] == $professor['id']) {
                                $checked = 'checked';
                            }
                            echo '<div class="form-check">';
                            echo '  <label class="form-check-label">';
                            echo '      <input type="checkbox" class="form-check-input" name="professores[]" value="' . htmlspecialchars($professor['id']) . '" ' . $checked . '>';
                            echo '      ' . htmlspecialchars($professor['nome']);
                            echo '  </label>';
                            echo '</div>';
                        }
                    }
                ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Faixas/Estrelas</label>
                <div id="faixas-container">
                    <?php if (!empty($faixas_cadastradas)): ?>
                        <?php foreach ($faixas_cadastradas as $faixa): ?>
                            <div class="mb-2 input-group">
                                <input type="text" class="form-control" name="faixas[]" placeholder="Nome da Faixa/Estrela" style="border-radius: 0.375rem;" value="<?php echo htmlspecialchars($faixa); ?>">
                                <button type="button" class="ms-2 remove-faixa">Remover</button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-faixa" class="btn btn-cinza mx-0 btn-sm mt-2 mb-0">Adicionar Faixa/Estrela</button>
            </div>
                <div class="d-flex w-100 mt-4 mb-5">
                    <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                    <a href="cadastro_modalidades.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
                    <?php if ($id_modalidade): ?>
                        <button type="submit" name="excluir" value="1" class="btn text-uppercase w-50 me-0 excluir" id="btn-excluir">Excluir</button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnExcluir = document.getElementById('btn-excluir');
            if (btnExcluir) {
                btnExcluir.addEventListener('click', function(event) {
                    if (!confirm('Tem certeza que deseja excluir esta modalidade? \n\nATENÇÃO: Todos os registros de alunos matriculados nesta modalidade serão permanentemente apagados. Esta ação não pode ser desfeita.')) {
                        event.preventDefault(); // Cancela o envio do formulário se o usuário clicar em "Cancelar"
                    }
                });
            }

            const faixasContainer = document.getElementById('faixas-container');

            // Função para adicionar o evento de remoção
            const addRemoveEvent = (button) => {
                button.addEventListener('click', function() {
                    this.parentElement.remove();
                });
            };

            // Adiciona evento aos botões de remover já existentes
            faixasContainer.querySelectorAll('.remove-faixa').forEach(addRemoveEvent);

            // Se não houver faixas, adiciona um campo vazio
            if (faixasContainer.children.length === 0) {
                document.getElementById('add-faixa').click();
            }

        });

        // Bloqueia a seleção de mais de 4 professores
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="professores[]"]');

            function updateCheckboxState() {
                const checkedCount = document.querySelectorAll('input[name="professores[]"]:checked').length;

                if (checkedCount >= 4) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.disabled = true;
                        }
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                    });
                }
            }
            checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateCheckboxState));
            updateCheckboxState();

            //Adiciona campos de faixa/estrela dinamicamente
            document.getElementById('add-faixa').addEventListener('click', function() {
                const container = document.getElementById('faixas-container');
                const div = document.createElement('div');
                div.className = 'input-group mb-2';

                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                input.name = 'faixas[]';
                input.placeholder = 'Nome da Faixa/Estrela';
                input.style.borderRadius = '0.375rem';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'ms-2 remove-faixa';
                removeBtn.textContent = 'Remover';
                removeBtn.addEventListener('click', () => div.remove());

                div.appendChild(input);
                div.appendChild(removeBtn);
                container.appendChild(div);
            });
        });
    </script>
</body>
</html>
