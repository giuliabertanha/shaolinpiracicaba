<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
} else if (!isset($_SESSION['admin']) || $_SESSION['admin'] == 0) {
    echo "<script>alert('Essa página exige acesso com um usuário administrador'); window.location.href = 'area_professor.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_modalidade_post = $_POST['id'] ?? null;

    //DELETE
    if (isset($_POST['excluir']) && !empty($id_modalidade_post)) {
        $stmt = $conn->prepare("DELETE FROM modalidades WHERE id = ?");
        $stmt->bind_param("i", $id_modalidade_post);
        if ($stmt->execute()) {
            echo "<script>alert('Modalidade excluída com sucesso!'); window.location.href = 'cadastro_modalidades.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir modalidade: " . $stmt->error . "'); window.location.href = 'cadastro_modalidades.php';</script>";
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    //UPDATE
    $nome = $_POST['nome'];
    $faixas = $_POST['faixas'] ?? [];

    if (!empty($id_modalidade_post)) {
        $stmt = $conn->prepare("UPDATE modalidades SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $nome, $id_modalidade_post);
    } 

    if ($stmt->execute()) {
        $novo_id = !empty($id_modalidade_post) ? $id_modalidade_post : $conn->insert_id;

        //Atualizando faixas/graduações
        if (!empty($id_modalidade_post)) {
            $stmt_delete_faixas = $conn->prepare("DELETE FROM graduacoes WHERE id_modalidade = ?");
            $stmt_delete_faixas->bind_param("i", $id_modalidade_post);
            $stmt_delete_faixas->execute();
            $stmt_delete_faixas->close();

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
];
$faixas_cadastradas = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_modalidade = $_GET['id'];
    $titulo_pagina = "Editar modalidade";

    $stmt = $conn->prepare("SELECT id, nome FROM modalidades WHERE id = ?");
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

    $stmt_faixas = $conn->prepare("SELECT nome FROM graduacoes WHERE id_modalidade = ? ORDER BY ordem");
    $stmt_faixas->bind_param("i", $id_modalidade);
    $stmt_faixas->execute();
    $result_faixas = $stmt_faixas->get_result();
    while ($faixa = $result_faixas->fetch_assoc()) {
        $faixas_cadastradas[] = $faixa['nome'];
    }
    $stmt_faixas->close();
}

$user_id = null;
$user_nome = '';
if (isset($_SESSION['usuario'])) {
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result_user = $stmt->get_result();
    if ($user = $result_user->fetch_assoc()) {
        $user_id = $user['id'];
        $user_nome = $user['nome'];
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
                    <a class="navbar-brand" href="index.php">
                        <img class="m-2" id="logo_cabecalho" src="img/logo.svg" alt="Logotipo">
                    </a>
                    <div class="flex-column">
                        <a href="index.php">
                            <h2 class="text-uppercase ms-2"><b id="titulo_cabecalho">Shaolin Kung Fu Piracicaba</b></h2>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <button class="btn-close d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Close"></button>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="index.php">Home</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-bs-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Modalidades
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="modalidades.php">Visão Geral</a></li>
                                        <li><a class="dropdown-item" href="shaolin.php">Shaolin do Norte</a></li>
                                        <li><a class="dropdown-item" href="kids.php">Shaolin Kids</a></li>
                                        <li><a class="dropdown-item" href="sanda.php">Sanda</a></li>
                                        <li><a class="dropdown-item" href="taichi.php">Tai Chi Chuan</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="sobre.php">Sobre</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="premiacoes.php">Premiações</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page" href="area_professor.php">Área do Professor</a>
                                </li>
                                <div id="user" class="d-flex align-items-center">
                                    <a href="meu_cadastro.php"><i class="fa-solid fa-user m-2" style="color: #161616;"></i></a>
                                    <span class="text-uppercase"><a href="meu_cadastro.php"><?php echo htmlspecialchars($user_nome); ?></a></span>
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
                <label class="form-label">Faixas/Estrelas</label>
                <div id="faixas-container">
                    <?php if (!empty($faixas_cadastradas)) { ?>
                        <?php foreach ($faixas_cadastradas as $faixa) { ?>
                            <div class="mb-2 input-group">
                                <input type="text" class="form-control" name="faixas[]" placeholder="Nome da Faixa/Estrela" style="border-radius: 0.375rem;" value="<?php echo htmlspecialchars($faixa); ?>">
                                <button type="button" class="ms-2 remove-faixa">Remover</button>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <button type="button" id="add-faixa" class="btn btn-cinza mx-0 btn-sm mt-2 mb-0">Adicionar Faixa/Estrela</button>
            </div>
                <div class="d-flex w-100 mt-4 mb-5">
                    <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                    <a href="cadastro_modalidades.php" class="btn text-uppercase w-50 voltar">Voltar</a>
                    <?php if ($id_modalidade) { ?>
                        <button type="submit" name="excluir" value="1" class="btn text-uppercase w-50 me-0 excluir" id="btn-excluir">Excluir</button>
                    <?php } ?>
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
                        event.preventDefault();
                    }
                });
            }

            const faixasContainer = document.getElementById('faixas-container');

            const addRemoveEvent = (button) => {
                button.addEventListener('click', function() {
                    this.parentElement.remove();
                });
            };

            faixasContainer.querySelectorAll('.remove-faixa').forEach(addRemoveEvent);

            if (faixasContainer.children.length === 0) {
                document.getElementById('add-faixa').click();
            }

        });

        document.addEventListener('DOMContentLoaded', function () {
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
