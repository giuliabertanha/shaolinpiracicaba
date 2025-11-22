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

$error_message = '';
$success_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $usuario_post = $_POST['usuario'];
    $senha_post = $_POST['senha'];
    if (empty($usuario_post) || empty($senha_post)) {
        $error_message = 'Por favor, preencha todos os campos.';
    } else {
        $stmt = $conn->prepare("SELECT id, usuario, senha, tipo, admin FROM usuarios WHERE usuario = ? LIMIT 1");
        $stmt->bind_param("s", $usuario_post);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($senha_post, $user['senha'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['usuario'] = $user['usuario'];
                $_SESSION['tipo'] = $user['tipo'];
                $_SESSION['admin'] = $user['admin'];
                if ($user['tipo'] == 'A') {
                    header("Location: area_aluno.php");
                } elseif ($user['tipo'] == 'P') {
                    header("Location: area_professor.php");
                }
                exit();
            } else {
                $error_message = 'Usuário ou senha incorretos.';
            }
        } else {
            $error_message = 'Usuário ou senha incorretos.';
        }
        $stmt->close();
    }
}
$conn->close();
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
    <title>Shaolin Piracicaba | Login</title>
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
                                    <a class="nav-link active" aria-current="page" href="login.php">Área do Aluno/Professor</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex align-items-center justify-content-center p-5">
        <div class="login-container w-100 p-4">
            <h2 class="text-uppercase mt-4 mb-3 text-center"><b>Login</b></h2>
            <form id="loginForm" action="login.php" method="POST" style="display: block;">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlenght="30" required>
                </div>
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" id="senha" name="senha" class="input-field" minlength="6" maxlenght="30" required>
                </div>
                <div class="forgot-password">
                    <a href="#" id="forgotPasswordLink">Esqueci minha senha</a>
                </div>
                <?php if (!empty($error_message)) { ?>
                    <div id="message" class="message error" style="display: block;"><?php echo $error_message; ?></div>
                <?php } ?>
                <?php if (!empty($success_message)) { ?>
                    <div id="message" class="message success" style="display: block;"><?php echo $success_message; ?></div>
                <?php } ?>
                <button type="submit" name="login" class="btn text-uppercase w-100 ms-0 mt-3 mb-3 btn_verde">Entrar</button>
            </form>

            <!-- Formulário de Esqueci a Senha -->
            <form id="forgotPasswordForm" action="login.php" method="POST" style="display: none;">
                <div class="mb-3">
                    <label for="usuario_email" class="form-label">Usuário ou E-mail</label>
                    <input type="text" class="input-field" name="usuario_email" id="usuario_email" autocomplete="off" required>
                </div>
                <button type="submit" name="forgot_password" class="btn text-uppercase w-100 ms-0 mt-3 mb-3 btn_verde">Enviar</button>
                <div class="text-center">
                    <a href="#" id="backToLoginLink">Voltar para o Login</a>
                </div>
            </form>

        </div>
    </main>
    <footer class="d-flex justify-content-between align-items-center p-4"> <!--Fixar rodapé-->
        <span>&copy; 2025 Shaolin Kung Fu Piracicaba - Todos os direitos reservados.</span>
        <div class="redes-sociais">
            <i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i>
            <i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i>
            <i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i>
        </div>
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('forgotPasswordForm').style.display = 'block';
            document.querySelector('.login-container h2').textContent = 'Recuperar Senha';
            const message = document.getElementById('message');
            if(message) message.style.display = 'none';
        });

        document.getElementById('backToLoginLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('forgotPasswordForm').style.display = 'none';
            document.getElementById('loginForm').style.display = 'block';
            document.querySelector('.login-container h2').textContent = 'Login';
            const message = document.getElementById('message');
            if(message) message.style.display = 'none';
        });
    </script>
</body>
</html>