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
        $admin = isset($_POST['admin']) ? 1 : 0;

        $query_consulta = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $result = $conn->query($query_consulta);

        if ($result->num_rows > 0) {
            echo "<script>alert('Este nome de usuário já está em uso.');</script>";
            exit();
        } else {
            $query_insert = "INSERT INTO usuarios (usuario, senha, nome, telefone, email, tipo, admin) VALUES ('$usuario', '$senha_hash', '$nome', '$telefone', '$email', 'P', '$admin')";

            if ($conn->query($query_insert) === TRUE) {
                echo "<script>alert('Dados salvos com sucesso!');</script>";
            } else {
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
    <title>Shaolin Piracicaba | Incluir Professor</title>
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
    <main class="d-flex flex-column align-items-center pt-2">
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir professor</b></h2>  
        <form class="w-75" action="incluir_professor.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" required>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2 w-50">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlength="30" required>
                    <div id="usuario-error" class="form-error"></div>
                </div>
                <div class="mx-2" style="width: 30%">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlength="30" autocomplete="off" required>
                    <div id="senha-error" class="form-error"></div>
                </div>
                <div class="ms-2" style="width: 20%">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" maxlength="15" required>
                    <div id="telefone-error" class="form-error"></div>
                </div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2" style="width: 88%">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" required>
                    <div id="email-error" class="form-error"></div>
                </div>
                <div class="ms-2 mt-5" style="width: 12%">
                    <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="admin" id="admin">
                        Administrador
                    </label>
                    </div>
                </div>
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
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            Faixa/Estrela
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Faixa Branca</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Amarela</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Azul</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Verde</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Vermelha</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Preta</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Azul</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Cinza</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Preta</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Azul Yin Yang</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Cinza Yin Yang</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Preta Yin Yang</a></li>
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
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            Faixa
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Faixa Branca Risco Preto</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Laranja</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Amarela com Risco Preto</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Roxa</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Azul Risco Preto</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Marrom</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Verde Risco Preto</a></li>
                            <li><a class="dropdown-item" href="#">Faixa Verde</a></li>
                            
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
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            Estrela
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Estrela com Contorno Prata e o Centro Preto</a></li>
                            <li><a class="dropdown-item" href="#">Estrela com Contorno Prata e o Centro Vermelho</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Prata</a></li>
                            <li><a class="dropdown-item" href="#">Estrela com Contorno Dourado e o Centro Preto</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Dourada com o Centro Vermelho</a></li>
                            <li><a class="dropdown-item" href="#">Estrela Dourada</a></li>
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
                </div>
            </div>
            <div class="d-flex w-100 mt-4 mb-5">
                <button type="submit" id="submit-button" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_professores.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //Habilita o dropdown de faixa/estrela apenas se a modalidade for selecionada
            const modalityRows = document.querySelectorAll('.mb-3 .d-flex.flex-row.w-100.justify-content-between.my-2');

            modalityRows.forEach(row => {
                const checkbox = row.querySelector('input[type="checkbox"].form-check-input');
                const dropdownButton = row.querySelector('.dropdown-bs-toggle');

                if (checkbox && dropdownButton) {
                    const originalButtonText = dropdownButton.textContent.trim();
 
                    dropdownButton.disabled = !checkbox.checked;

                    checkbox.addEventListener('change', function() {
                        dropdownButton.disabled = !this.checked;
                        if (!this.checked) {
                            // Se desmarcado, restaura o texto original do botão
                            dropdownButton.textContent = originalButtonText;
                        }
                    });
                }
            });

            //Validação do formulário
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                let errorMessage = '';

                modalityRows.forEach(row => {
                    const checkbox = row.querySelector('input[type="checkbox"].form-check-input');
                    const dropdownButton = row.querySelector('.dropdown-bs-toggle');
                    const modalityName = row.querySelector('.form-check-label').textContent.trim();

                    //Faixa/estrela não selecionada
                    if (checkbox.checked && dropdownButton.textContent.trim() === 'Faixa/Estrela') {
                        isValid = false;
                        errorMessage = `Por favor, selecione a Faixa/Estrela para a modalidade "${modalityName}".`;
                    }
                });

                if (!isValid) {
                    alert(errorMessage);
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
