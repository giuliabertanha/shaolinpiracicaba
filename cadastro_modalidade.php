<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

// Função para formatar o nome da modalidade e criar um nome de tabela válido
function formatar_nome_tabela($nome, $conn) {
    // Remove acentos e caracteres especiais
    $nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    // Converte para minúsculas
    $nome_minusculo = strtolower($nome_sem_acentos);
    // Substitui espaços e outros caracteres não alfanuméricos por underscore
    $nome_tabela = preg_replace('/[^a-z0-9_]+/', '_', $nome_minusculo);
    // Remove underscores duplicados ou no início/fim
    $nome_tabela = trim($nome_tabela, '_');
    // Escapa a string para segurança final (embora o uso de crases já proteja)
    return mysqli_real_escape_string($conn, $nome_tabela);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_modalidade_post = $_POST['id'] ?? null;

    //DELETE
    if (isset($_POST['excluir']) && !empty($id_modalidade_post)) {
        // 1. Busca o nome da modalidade para saber qual tabela apagar
        $stmt_get_nome = $conn->prepare("SELECT nome FROM modalidades WHERE id = ?");
        $stmt_get_nome->bind_param("i", $id_modalidade_post);
        $stmt_get_nome->execute();
        $nome_modalidade = $stmt_get_nome->get_result()->fetch_assoc()['nome'];
        $stmt_get_nome->close();

        // 2. Deleta o registro da modalidade
        $stmt = $conn->prepare("DELETE FROM modalidades WHERE id = ?");
        $stmt->bind_param("i", $id_modalidade_post);
        if ($stmt->execute()) {
            // 3. Se teve sucesso, apaga a tabela correspondente
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

     // Validação do número de professores
    if (count($professores_selecionados) > 2) {
        echo "<script>alert('Você só pode selecionar no máximo 2 professores.'); window.history.back();</script>";
        exit;
    }

    $id_professor1 = isset($professores_selecionados[0]) ? (int)$professores_selecionados[0] : null;
    $id_professor2 = isset($professores_selecionados[1]) ? (int)$professores_selecionados[1] : null;

    if (!empty($id_modalidade_post)) {
        // UPDATE
        $stmt = $conn->prepare("UPDATE modalidades SET nome = ?, id_professor1 = ?, id_professor2 = ? WHERE id = ?");
        $stmt->bind_param("siii", $nome, $id_professor1, $id_professor2, $id_modalidade_post);
    }    // } else {
    //     // INSERT
    //     $stmt = $conn->prepare("INSERT INTO modalidades (nome, id_professor1, id_professor2) VALUES (?, ?, ?)");
    //     $stmt->bind_param("sii", $nome, $id_professor1, $id_professor2);
    // }

    if ($stmt->execute()) {
        $novo_id = !empty($id_modalidade_post) ? $id_modalidade_post : $conn->insert_id;
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
];

$sql_professores = "SELECT id, nome FROM usuarios WHERE tipo = 'P' ORDER BY nome";
$result_professores = $conn->query($sql_professores);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_modalidade = $_GET['id'];
    $titulo_pagina = "Editar modalidade";

    $stmt = $conn->prepare("SELECT id, nome, id_professor1, id_professor2 FROM modalidades WHERE id = ?");
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
                            if ($modalidade['id_professor1'] == $professor['id'] || $modalidade['id_professor2'] == $professor['id']) {
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
        });

        // Bloqueia a seleção de mais de 2 professores
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="professores[]"]');

            function updateCheckboxState() {
                const checkedCount = document.querySelectorAll('input[name="professores[]"]:checked').length;

                if (checkedCount > 2) {
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
        });
    </script>
</body>
</html>
