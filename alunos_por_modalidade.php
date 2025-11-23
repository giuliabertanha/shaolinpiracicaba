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
} else if ($_SESSION['tipo'] != 'P') {
    echo "<script>alert('Essa página exige acesso com um usuário de professor.'); window.location.href = 'login.php';</script>";
}

$sql_modalidades = "SELECT id, nome FROM modalidades ORDER BY nome";
$result_modalidades = $conn->query($sql_modalidades);

$alunos_da_modalidade = [];
$nome_modalidade_selecionada = '';
$id_modalidade_selecionada = $_GET['modalidade_id'] ?? null;

if ($id_modalidade_selecionada) {
    $stmt_nome = $conn->prepare("SELECT nome FROM modalidades WHERE id = ?");
    $stmt_nome->bind_param("i", $id_modalidade_selecionada);
    $stmt_nome->execute();
    $result_nome = $stmt_nome->get_result();
    if ($row_nome = $result_nome->fetch_assoc()) {
        $nome_modalidade_selecionada = $row_nome['nome'];
    }
    $stmt_nome->close();
    
    if ($nome_modalidade_selecionada) {
        $stmt = $conn->prepare(
            "SELECT usuarios.id as 'id_aluno', usuarios.nome as 'aluno_nome', graduacoes.nome as 'graduacao_nome' 
             FROM matriculas 
             INNER JOIN usuarios ON usuarios.id = matriculas.id_usuario 
             INNER JOIN graduacoes ON graduacoes.id = matriculas.id_graduacao 
             WHERE matriculas.id_modalidade = ? AND usuarios.tipo = 'A'
             ORDER BY usuarios.nome"
        );
        $stmt->bind_param("i", $id_modalidade_selecionada);
        $stmt->execute();
        $result_alunos = $stmt->get_result();

        if ($result_alunos->num_rows > 0) {
            $alunos_da_modalidade = $result_alunos->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}

$user_id = null;
$user_nome = '';
if (isset($_SESSION['usuario'])) {
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="img/icon.png" type="image/x-icon">
    <title>Shaolin Piracicaba | Alunos por modalidade</title>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Alunos por modalidade</b></h2>  
        <div class="w-75">
            <form action="alunos_por_modalidade.php" method="GET" class="d-flex mb-4 align-items-center">
                <select name="modalidade_id" class="form-select me-2" required>
                    <option value="">Selecione uma modalidade</option>
                    <?php
                    if ($result_modalidades && $result_modalidades->num_rows > 0) {
                        $result_modalidades->data_seek(0); // Reinicia o ponteiro do resultado
                        while ($modalidade = $result_modalidades->fetch_assoc()) {
                            $selected = ($id_modalidade_selecionada == $modalidade['id']) ? 'selected' : '';
                            echo '<option value="' . $modalidade['id'] . '" ' . $selected . '>' . htmlspecialchars($modalidade['nome']) . '</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn_verde" style="white-space: nowrap;">Gerar Relatório</button>
            </form>

            <?php if ($id_modalidade_selecionada) { ?>
                <table class="table table-striped rounded">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2"><?php echo htmlspecialchars($nome_modalidade_selecionada); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($alunos_da_modalidade)) { ?>
                            <?php foreach ($alunos_da_modalidade as $aluno) { ?>
                                <tr>
                                    <td><a href="cadastro_aluno.php?id=<?php echo $aluno['id_aluno']; ?>"><?php echo htmlspecialchars($aluno['aluno_nome']); ?></a></td>
                                    <td style="width: 35%;"><?php echo htmlspecialchars($aluno['graduacao_nome']); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr><td colspan="2" class="text-center">Nenhum aluno encontrado para esta modalidade.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </div>
        <a class="btn text-uppercase w-75 btn_verde" href="area_professor.php">Voltar</a>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>