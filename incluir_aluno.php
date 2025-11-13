<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        $query_consulta = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $result = $conn->query($query_consulta);

        if ($result->num_rows > 0) {
            echo "<script>alert('Este nome de usuário já está em uso.');</script>";
            exit();
        } else {
            $query_insert = "INSERT INTO usuarios (usuario, senha, nome, telefone, email, tipo, admin) VALUES ('$usuario', '$senha_hash', '$nome', '$telefone', '$email', 'A', '0')";

            if ($conn->query($query_insert) === TRUE) {
                // Dados salvos com sucesso
                echo "<script>alert('Dados salvos com sucesso!');</script>";
            } else {
                // Erro ao salvar os dados
                echo "<script>alert('Erro ao inserir dados: " . $conn->error . "');</script>";
            }
        }
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
    <title>Shaolin Piracicaba | Incluir Aluno</title>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir aluno</b></h2>  
        <form class="w-75" action="incluir_aluno.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="usuario" class="form-label">Usuário</label>
                <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlenght="30" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="modalidades" class="form-label">Modalidades</label>
                <div class="d-flex flex-row w-100 justify-content-between my-2">    
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="">
                        Shaolin do Norte
                        </label>
                    </div>
                    <div class="dropdown">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Faixa
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Opção 1</a></li>
                            <li><a class="dropdown-item" href="#">Opção 2</a></li>
                            <li><a class="dropdown-item" href="#">Opção 3</a></li>
                            <li><a class="dropdown-item" href="#">Opção 4</a></li>
                            <li><a class="dropdown-item" href="#">Opção 5</a></li>
                            <li><a class="dropdown-item" href="#">Opção 6</a></li>
                            <li><a class="dropdown-item" href="#">Opção 7</a></li>
                            <li><a class="dropdown-item" href="#">Opção 8</a></li>
                            <li><a class="dropdown-item" href="#">Opção 9</a></li>
                            <li><a class="dropdown-item" href="#">Opção 10</a></li>
                            <li><a class="dropdown-item" href="#">Opção 11</a></li>
                            <li><a class="dropdown-item" href="#">Opção 12</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex flex-row w-100 justify-content-between my-2">    
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="">
                        Shaolin Kids
                        </label>
                    </div>
                    <div class="dropdown">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Faixa
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Opção 1</a></li>
                            <li><a class="dropdown-item" href="#">Opção 2</a></li>
                            <li><a class="dropdown-item" href="#">Opção 3</a></li>
                            <li><a class="dropdown-item" href="#">Opção 4</a></li>
                            <li><a class="dropdown-item" href="#">Opção 5</a></li>
                            <li><a class="dropdown-item" href="#">Opção 6</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex flex-row w-100 justify-content-between my-2">    
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="">
                        Sanda - Boxe Chinês
                        </label>
                    </div>
                    <div class="dropdown">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Estrela
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Opção 1</a></li>
                            <li><a class="dropdown-item" href="#">Opção 2</a></li>
                            <li><a class="dropdown-item" href="#">Opção 3</a></li>
                            <li><a class="dropdown-item" href="#">Opção 4</a></li>
                            <li><a class="dropdown-item" href="#">Opção 5</a></li>
                            <li><a class="dropdown-item" href="#">Opção 6</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex flex-row w-100 justify-content-between my-2">    
                    <div class="form-check">
                        <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="" id="">
                        Tai Chi Chuan
                        </label>
                    </div>
                    <div class="dropdown">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Faixa
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Opção 1</a></li>
                            <li><a class="dropdown-item" href="#">Opção 2</a></li>
                            <li><a class="dropdown-item" href="#">Opção 3</a></li>
                            <li><a class="dropdown-item" href="#">Opção 4</a></li>
                            <li><a class="dropdown-item" href="#">Opção 5</a></li>
                            <li><a class="dropdown-item" href="#">Opção 6</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="input-field" name="email" id="email" autocomplete="off" required>
            </div>
            <div class="d-flex w-100 mt-4 mb-5">
                <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_alunos.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>