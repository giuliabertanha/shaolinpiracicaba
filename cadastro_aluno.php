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

// Formatando o nome da modalidade para um nome de tabela válido
function formatar_nome_tabela($nome) {
    $nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    $nome_minusculo = strtolower($nome_sem_acentos);
    $nome_tabela = preg_replace('/[^a-z0-9_]+/', '_', $nome_minusculo);
    $nome_tabela = trim($nome_tabela, '_');
    return $nome_tabela;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aluno_post = $_POST['id'] ?? null;

    // --- EXCLUSÃO ---
    if (isset($_POST['excluir']) && !empty($id_aluno_post)) {
        $conn->begin_transaction();
        try {
            // 1. Buscar todas as modalidades para limpar as tabelas dinâmicas
            $sql_todas_modalidades = "SELECT nome FROM modalidades";
            $result_todas_modalidades = $conn->query($sql_todas_modalidades);

            if ($result_todas_modalidades) {
                while ($mod = $result_todas_modalidades->fetch_assoc()) {
                    $nome_tabela = formatar_nome_tabela($mod['nome']);
                    $stmt_delete_graduacao = $conn->prepare("DELETE FROM `$nome_tabela` WHERE id_aluno = ?");
                    if ($stmt_delete_graduacao) {
                        $stmt_delete_graduacao->bind_param("i", $id_aluno_post);
                        $stmt_delete_graduacao->execute();
                        $stmt_delete_graduacao->close();
                    }
                }
            }

            // 2. Excluir registros da tabela de matrículas
            $stmt_delete_matriculas = $conn->prepare("DELETE FROM matriculas WHERE id_aluno = ?");
            $stmt_delete_matriculas->bind_param("i", $id_aluno_post);
            $stmt_delete_matriculas->execute();
            $stmt_delete_matriculas->close();

            // 3. Excluir o usuário aluno
            $stmt_delete_user = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND tipo = 'A'");
            $stmt_delete_user->bind_param("i", $id_aluno_post);
            $stmt_delete_user->execute();
            $stmt_delete_user->close();

            $conn->commit();
            echo "<script>alert('Aluno excluído com sucesso!'); window.location.href = 'cadastro_alunos.php';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Erro ao excluir aluno: " . $e->getMessage() . "'); window.history.back();</script>";
        }
        $conn->close();
        exit;
    }
}

$id_aluno = null;
$aluno = [
    'nome' => '',
    'usuario' => '',
    'telefone' => '',
    'email' => ''
];

$aluno_modalidades = []; // Armazena as modalidades e graduações do aluno

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_aluno = $_GET['id'];
    $titulo_pagina = "Cadastro do aluno";

    $stmt = $conn->prepare("SELECT nome, usuario, telefone, email, admin FROM usuarios WHERE id = ? AND tipo = 'A'");
    $stmt->bind_param("i", $id_aluno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
    } else {
        echo "<script>alert('Aluno não encontrado!'); window.location.href = 'cadastro_alunos.php';</script>";
        exit;
    }
    $stmt->close();
}

$sql_modalidades = "SELECT id, nome FROM modalidades ORDER BY nome";
$result_modalidades = $conn->query($sql_modalidades);
$modalidades_disponiveis = [];
if ($result_modalidades && $result_modalidades->num_rows > 0) {
    while($row = $result_modalidades->fetch_assoc()) {
        $modalidades_disponiveis[] = $row;
        if ($id_aluno) {
            $nome_tabela = formatar_nome_tabela($row['nome']);
            $stmt_graduacao = $conn->prepare("SELECT faixa FROM `$nome_tabela` WHERE id_aluno = ?");
            if ($stmt_graduacao) {
                $stmt_graduacao->bind_param("i", $id_aluno);
                $stmt_graduacao->execute();
                $graduacao_result = $stmt_graduacao->get_result();
                if ($graduacao_result->num_rows > 0) {
                    $aluno_modalidades[$row['id']] = $graduacao_result->fetch_assoc()['faixa'];
                }
                $stmt_graduacao->close();
            }
        }
    }
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
    <title>Shaolin Piracicaba | Cadastro do Aluno</title>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b><?php echo $titulo_pagina; ?></b></h2>  
        <form class="w-75" action="cadastro_aluno.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_aluno); ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($aluno['nome']); ?>" required>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2 w-50">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlength="30" value="<?php echo htmlspecialchars($aluno['usuario']); ?>" required>
                    <div id="usuario-error" class="form-error"></div>
                </div>
                <div class="mx-2" style="width: 30%">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlength="30" autocomplete="off" placeholder="<?php echo $id_aluno ? 'Deixe em branco para não alterar' : ''; ?>" <?php if (!$id_aluno) echo 'required'; ?>>
                    <div id="senha-error" class="form-error"></div>
                </div>
                <div class="ms-2" style="width: 20%">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" maxlength="15" value="<?php echo htmlspecialchars($aluno['telefone']); ?>" required>
                    <div id="telefone-error" class="form-error"></div>
                </div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2" style="width: 88%">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($aluno['email']); ?>" required>
                    <div id="email-error" class="form-error"></div>
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
                <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_alunos.php" class="btn text-uppercase w-50 voltar">Voltar</a>
                <?php if ($id_aluno) { ?>
                    <button type="submit" name="excluir" value="1" class="btn text-uppercase w-50 me-0 excluir" id="btn-excluir">Excluir</button>
                <?php } ?>
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

            // Adiciona a confirmação para o botão de excluir
            const btnExcluir = document.getElementById('btn-excluir');
            if (btnExcluir) {
                btnExcluir.addEventListener('click', function(event) {
                    if (!confirm('Tem certeza que deseja excluir este cadastro? Esta ação não pode ser desfeita.')) {
                        event.preventDefault(); // Cancela o envio do formulário se o usuário clicar em "Cancelar"
                    }
                });
            }
        });
    </script>
</body>
</html>
