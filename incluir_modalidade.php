<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

$sql_professores = "SELECT id, nome FROM usuarios WHERE tipo = 'P' ORDER BY nome";
$result_professores = $conn->query($sql_professores);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'];
        $professores_selecionados = isset($_POST['professores']) ? $_POST['professores'] : [];

        $id_professor1 = isset($professores_selecionados[0]) ? $professores_selecionados[0] : 'NULL';
        $id_professor2 = isset($professores_selecionados[1]) ? $professores_selecionados[1] : 'NULL';

        $stmt = $conn->prepare("INSERT INTO modalidades (nome, id_professor1, id_professor2) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $nome, $id_professor1, $id_professor2); //sii = string integer integer

        if ($stmt->execute()) {
            echo "<script>alert('Dados salvos com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao inserir dados: " . $stmt->error . "');</script>";
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
                                <li class="nav-item">
                                    <a class="nav-link" href="modalidades.html">Modalidades</a>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir modalidade</b></h2>  
        <form class="w-75" action="incluir_modalidade.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" id="nome" required>
            </div>
            <div class="mb-3">
                <label for="professores" class="form-label">Professores</label>  
                <?php
                if ($result_professores->num_rows > 0) {
                    while($professor = $result_professores->fetch_assoc()) {
                        echo '<div class="form-check">';
                        echo '  <label class="form-check-label">';
                        echo '      <input type="checkbox" class="form-check-input" name="professores[]" value="' . htmlspecialchars($professor['id']) . '">';
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
                </div>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
