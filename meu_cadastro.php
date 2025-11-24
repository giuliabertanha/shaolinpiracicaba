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

// Lógica de permissão: Apenas para usuários logados (professor ou aluno)
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo'];
$area_usuario_link = ($tipo_usuario == 'P') ? 'area_professor.php' : 'area_aluno.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // A verificação do ID do post deve ser contra o ID da sessão
    $id_usuario_post = $_POST['id'] ?? null;

    if ($id_usuario_post != $id_usuario) {
        // Tentativa de manipulação de dados de outro usuário
        echo "<script>alert('Acesso negado.'); window.location.href = '$area_usuario_link';</script>";
        exit;
    }

    // Lógica de atualização (sem exclusão, pois o usuário não deve excluir a própria conta)
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        $conn->begin_transaction();
        try {
            // Apenas a senha pode ser alterada nesta tela.
            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_update_user = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                $stmt_update_user->bind_param("si", $senha_hash, $id_usuario);
                $stmt_update_user->execute();
                $stmt_update_user->close();
                $conn->commit();
                echo "<script>alert('Sua senha foi atualizada com sucesso!'); window.location.href = 'meu_cadastro.php';</script>";
            } else {
                // Se a senha estiver em branco, não faz nada, apenas recarrega a página.
                $conn->rollback(); // Cancela a transação
                echo "<script>alert('Nenhuma alteração foi feita.'); window.location.href = 'meu_cadastro.php';</script>";
            }
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Erro ao atualizar senha: " . $e->getMessage() . "'); window.history.back();</script>";
        }
        exit;
    }
}

$usuario_data = [
    'nome' => '',
    'usuario' => '',
    'telefone' => '',
    'email' => '',
];

$usuario_modalidades = [];

$stmt = $conn->prepare("SELECT nome, usuario, telefone, email FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario_data = $result->fetch_assoc();
} else {
    echo "<script>alert('Erro: Perfil não encontrado!'); window.location.href = 'login.php';</script>";
    exit;
}
$stmt->close();

$sql_matriculas = "
    SELECT m.nome AS modalidade_nome, g.nome AS graduacao_nome
    FROM matriculas mat
    JOIN modalidades m ON mat.id_modalidade = m.id
    JOIN graduacoes g ON mat.id_graduacao = g.id
    WHERE mat.id_usuario = ?
    ORDER BY m.nome";
$stmt_matriculas = $conn->prepare($sql_matriculas);
$stmt_matriculas->bind_param("i", $id_usuario);
$stmt_matriculas->execute();
$result_matriculas = $stmt_matriculas->get_result();

if (!$result_matriculas) {
    die("Erro na consulta de matrículas: " . $conn->error);
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
    <title>Shaolin Piracicaba | Meu Cadastro</title>
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
                                    <a class="nav-link active" aria-current="page" href="<?php echo $area_usuario_link; ?>">Área do Aluno/Professor</a>
                                </li>
                                <div id="user" class="d-flex align-items-center">
                                    <a href="meu_cadastro.php"><i class="fa-solid fa-user m-2" style="color: #161616;"></i></a>
                                    <span class="text-uppercase"><a href="meu_cadastro.php"><?php echo htmlspecialchars($_SESSION['usuario']); ?></a></span>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex flex-column align-items-center">
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Meu Cadastro</b></h2>  
        <form class="w-75" action="meu_cadastro.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_usuario); ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($usuario_data['nome']); ?>" readonly>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="d-flex div-form mb-3">
                <div class="me-2 w-50">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlength="30" value="<?php echo htmlspecialchars($usuario_data['usuario']); ?>" readonly>
                    <div id="usuario-error" class="form-error"></div>
                </div>
                <div class="mx-2" style="width: 30%">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlength="30" autocomplete="off" placeholder="Alterar senha">
                    <div id="senha-error" class="form-error"></div>
                </div>
                <div class="ms-2" style="width: 20%">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" maxlength="15" value="<?php echo htmlspecialchars($usuario_data['telefone']); ?>" readonly>
                    <div id="telefone-error" class="form-error"></div>
                </div>
            </div>
            <div class="d-flex div-form mb-3">
                <div class="me-2" style="width: 100%">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($usuario_data['email']); ?>" readonly>
                    <div id="email-error" class="form-error"></div>
                </div>
            </div>

            <div class="mb-3">
                <label for="modalidades" class="form-label">Modalidades</label>
                <?php if ($result_matriculas->num_rows > 0) { ?>
                    <?php while($matricula = $result_matriculas->fetch_assoc()) { ?>
                        <div class="d-flex flex-row w-100 justify-content-between my-2">
                            <div class="p-2 bg-light border rounded w-50 me-2"><?php echo htmlspecialchars($matricula['modalidade_nome']); ?></div>
                            <div class="p-2 bg-light border rounded w-50 ms-2"><?php echo htmlspecialchars($matricula['graduacao_nome']); ?></div>
                        </div>
                    <?php } ?>
                    <?php $stmt_matriculas->close(); ?>
                <?php } else { ?>
                    <p>Nenhuma modalidade encontrada.</p>
                <?php } ?>
            </div>
            <div class="d-flex w-100 space-between mt-4 mb-5">
                <button type="submit" class="btn text-uppercase ms-0 btn_verde" style="width: 33%">Salvar</button>
                <a href="<?php echo $area_usuario_link; ?>" class="btn ms-0 text-uppercase voltar" style="width: 33%">Voltar</a>
                <a href="logout.php" class="btn text-uppercase ms-0 excluir" style="width: 33%">Sair</a>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //Habilita o dropdown de faixa/estrela apenas se a modalidade for selecionada
            const linhasModalidade = document.querySelectorAll('.mb-3 .d-flex.flex-row.w-100.justify-content-between.my-2');

            linhasModalidade.forEach(linha => {
                const caixaSelecao = linha.querySelector('input[type="checkbox"].form-check-input');
                const botaoDropdown = linha.querySelector('.dropdown-bs-toggle');
                const inputOculto = linha.querySelector('input[type="hidden"]');

                if (caixaSelecao && botaoDropdown) {
                    const textoOriginalBotao = 'Faixa/Estrela';
 
                    const atualizarEstado = () => {
                        botaoDropdown.disabled = !caixaSelecao.checked;
                        if (!caixaSelecao.checked) {
                            botaoDropdown.textContent = textoOriginalBotao;
                            if (inputOculto) {
                                inputOculto.value = '';
                            }
                        }
                    };

                    caixaSelecao.addEventListener('change', atualizarEstado);
                    
                    atualizarEstado();
                }

            });

            const formulario = document.querySelector('form');
            formulario.addEventListener('submit', function(evento) {
                let eValido = true;
                let mensagemErro = '';

                linhasModalidade.forEach(linha => {
                    const caixaSelecao = linha.querySelector('input[type="checkbox"].form-check-input');
                    const botaoDropdown = linha.querySelector('.dropdown-bs-toggle');
                    const nomeModalidade = linha.querySelector('.form-check-label').textContent.trim();

                    // Se a modalidade está marcada, mas a faixa/estrela não foi selecionada (e existe um dropdown)
                    if (caixaSelecao.checked && botaoDropdown && botaoDropdown.textContent.trim() === 'Faixa/Estrela') {
                        eValido = false;
                        mensagemErro = `Por favor, selecione a Faixa/Estrela para a modalidade "${nomeModalidade}".`;
                    }
                });

                if (!eValido) {
                    alert(mensagemErro);
                    evento.preventDefault();
                }
            });
        });
    </script>
</body>
</html>