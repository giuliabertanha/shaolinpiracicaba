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

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'P') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $modalidades_selecionadas = $_POST['modalidades'] ?? [];

    // Verifica se o usuário já existe
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt_check->bind_param("s", $usuario);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<script>alert('Este nome de usuário já está em uso.'); window.history.back();</script>";
        $stmt_check->close();
        exit;
    }
    $stmt_check->close();

    $conn->begin_transaction();

    try {
        // Insere o novo aluno
        $stmt_insert = $conn->prepare("INSERT INTO usuarios (usuario, senha, nome, telefone, email, tipo, admin) VALUES (?, ?, ?, ?, ?, 'A', 0)");
        $stmt_insert->bind_param("sssss", $usuario, $senha_hash, $nome, $telefone, $email);
        $stmt_insert->execute();
        $id_novo_aluno = $conn->insert_id;
        $stmt_insert->close();

        // Insere as matrículas
        foreach ($modalidades_selecionadas as $id_modalidade => $dados) {
            // Se a modalidade foi selecionada e uma graduação foi escolhida
            if (isset($dados['selecionada']) && !empty($dados['graduacao'])) {
                $nome_graduacao = $dados['graduacao'];

                // Busca o ID da graduação pelo nome
                $stmt_grad = $conn->prepare("SELECT id FROM graduacoes WHERE nome = ? AND id_modalidade = ?");
                $stmt_grad->bind_param("si", $nome_graduacao, $id_modalidade);
                $stmt_grad->execute();
                $result_grad = $stmt_grad->get_result();

                if ($grad_row = $result_grad->fetch_assoc()) {
                    $id_graduacao = $grad_row['id'];
                    $stmt_matricula = $conn->prepare("INSERT INTO matriculas (id_usuario, id_modalidade, id_graduacao) VALUES (?, ?, ?)");
                    $stmt_matricula->bind_param("iii", $id_novo_aluno, $id_modalidade, $id_graduacao);
                    $stmt_matricula->execute();
                    $stmt_matricula->close();
                }
                $stmt_grad->close();
            }
        }
        $conn->commit();
        echo "<script>alert('Aluno cadastrado com sucesso!'); window.location.href = 'cadastro_alunos.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Erro ao cadastrar aluno: " . $e->getMessage() . "'); window.history.back();</script>";
    }
    exit;
}

$sql_modalidades = "SELECT id, nome FROM modalidades ORDER BY nome";
$result_modalidades = $conn->query($sql_modalidades);
$modalidades_disponiveis = [];
if ($result_modalidades && $result_modalidades->num_rows > 0) {
    while($row = $result_modalidades->fetch_assoc()) {
        $stmt_graduacoes = $conn->prepare("SELECT nome FROM graduacoes WHERE id_modalidade = ? ORDER BY ordem");
        $stmt_graduacoes->bind_param("i", $row['id']);
        $stmt_graduacoes->execute();
        $modalidades_disponiveis[] = ['modalidade' => $row, 'graduacoes' => $stmt_graduacoes->get_result()->fetch_all(MYSQLI_ASSOC)];
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
                                    <a class="nav-link active" aria-current="page" href="area_professor.php">Área do Professor</a>
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
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" required>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2 w-50">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlength="30" required>
                    <div id="usuario-error" class="form-error"></div>
                </div>
                <div class="mx-2 w-50">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlength="30" autocomplete="off" required>
                    <div id="senha-error" class="form-error"></div>
                </div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2 w-50">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" required>
                    <div id="email-error" class="form-error"></div>
                </div>
                <div class="ms-2 w-50">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" maxlength="15" required>
                    <div id="telefone-error" class="form-error"></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="modalidades" class="form-label">Modalidades</label>
                <?php foreach ($modalidades_disponiveis as $item) {
                    $id_modalidade = $item['modalidade']['id'];
                    $nome_modalidade = htmlspecialchars($item['modalidade']['nome']);
                    $graduacoes = $item['graduacoes'];
                ?>
                <div class="d-flex flex-row w-100 justify-content-between my-2">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="modalidades[<?php echo $id_modalidade; ?>][selecionada]" value="1">
                            <?php echo $nome_modalidade; ?>
                        </label>
                    </div>
                    
                    <?php if (!empty($graduacoes)){ ?>
                    <div class="dropdown">
                        <input type="hidden" name="modalidades[<?php echo $id_modalidade; ?>][graduacao]" value="">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            Faixa/Estrela
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($graduacoes as $graduacao) { ?>
                            <li><a class="dropdown-item" href="#"><?php echo htmlspecialchars($graduacao['nome']); ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <div class="d-flex w-100 mt-4 mb-5">
                <button type="submit" id="submit-button" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_alunos.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
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

                    // Atualiza o botão e o input hidden com a graduação selecionada
                    const dropdownItems = linha.querySelectorAll('.dropdown-item');
                    dropdownItems.forEach(item => {
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            const nomeGraduacao = this.textContent;
                            if (botaoDropdown && inputOculto) {
                                botaoDropdown.textContent = nomeGraduacao;
                                inputOculto.value = nomeGraduacao;
                            }
                        });
                    });
                    
                    atualizarEstado();
                }
            });

            //Validação do formulário
            const form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                let errorMessage = '';

                linhasModalidade.forEach(linha => {
                    const caixaSelecao = linha.querySelector('input[type="checkbox"].form-check-input');
                    const botaoDropdown = linha.querySelector('.dropdown-bs-toggle');
                    const nomeModalidade = linha.querySelector('.form-check-label').textContent.trim();

                    //Faixa/estrela não selecionada
                    if (caixaSelecao.checked && botaoDropdown && botaoDropdown.textContent.trim() === 'Faixa/Estrela') {
                        isValid = false;
                        errorMessage = `Por favor, selecione a Faixa/Estrela para a modalidade "${nomeModalidade}".`;
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