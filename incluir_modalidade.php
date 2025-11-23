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
    echo "<script>alert('Essa página exige acesso com um usuário administrador.'); window.location.href = 'cadastro_modalidades.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $faixas = $_POST['faixas'] ?? [];

    $stmt_check = $conn->prepare("SELECT id FROM modalidades WHERE nome = ?");
    $stmt_check->bind_param("s", $nome);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo "<script>alert('Já existe uma modalidade com este nome. Por favor, escolha outro.'); window.history.back();</script>";
        exit;
    }
    $stmt_check->close();

    $stmt = $conn->prepare("INSERT INTO modalidades (nome) VALUES (?)");
    $stmt->bind_param("s", $nome);

    if ($stmt->execute()) {
        $novo_id = $conn->insert_id;

        if (!empty($faixas)) {
            $stmt_faixa = $conn->prepare("INSERT INTO graduacoes (id_modalidade, nome, ordem) VALUES (?, ?, ?)");
            $ordem = 1;
            foreach ($faixas as $faixa_nome) {
                $faixa_nome_trim = trim($faixa_nome);
                if (!empty($faixa_nome_trim)) {
                    $stmt_faixa->bind_param("isi", $novo_id, $faixa_nome_trim, $ordem);
                    $stmt_faixa->execute();
                    $ordem++;
                }
            }
            $stmt_faixa->close();
        }
        
        echo "<script>alert('Modalidade criada com sucesso!'); window.location.href = 'cadastro_modalidades.php';</script>";

    } else {
        echo "<script>alert('Erro ao inserir dados: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
    exit;
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
    <title>Shaolin Piracicaba | Incluir Modalidade</title>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir modalidade</b></h2>  
        <form class="w-75" action="incluir_modalidade.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Faixas/Estrelas</label>
                <div id="faixas-container">
                    <input type="text" class="form-control mb-2" name="faixas[]" placeholder="Nome da Faixa/Estrela">
                </div>
                <button type="button" id="add-faixa" class="btn btn-cinza mx-0 btn-sm mt-2 mb-0">Adicionar Faixa/Estrela</button>
            </div>
            <div class="d-flex w-100 mt-4 mb-5">
                <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_modalidades.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('add-faixa').addEventListener('click', function() {
                const container = document.getElementById('faixas-container');
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.className = 'form-control mb-2';
                newInput.name = 'faixas[]';
                newInput.placeholder = 'Nome da Faixa/Estrela';
                container.appendChild(newInput);
            });
        });
    </script>
</body>
</html>
