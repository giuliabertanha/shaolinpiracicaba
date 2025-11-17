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

// Função para formatar o nome da modalidade para um nome de tabela válido
function formatar_nome_tabela($nome) {
    $nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    $nome_minusculo = strtolower($nome_sem_acentos);
    $nome_tabela = preg_replace('/[^a-z0-9_]+/', '_', $nome_minusculo);
    $nome_tabela = trim($nome_tabela, '_');
    return $nome_tabela;
}

$id_professor = null;
$professor = [
    'nome' => '',
    'usuario' => '',
    'telefone' => '',
    'email' => '',
    'admin' => ''
];

$professor_modalidades = []; // Armazena as modalidades e graduações do professor

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_professor = $_GET['id'];
    $titulo_pagina = "Cadastro do professor";

    $stmt = $conn->prepare("SELECT nome, usuario, telefone, email, admin FROM usuarios WHERE id = ? AND tipo = 'P'");
    $stmt->bind_param("i", $id_professor);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $professor = $result->fetch_assoc();
    } else {
        echo "<script>alert('Professor não encontrado!'); window.location.href = 'cadastro_professores.php';</script>";
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
        if ($id_professor) {
            $nome_tabela = formatar_nome_tabela($row['nome']);
            $stmt_graduacao = $conn->prepare("SELECT faixa FROM `$nome_tabela` WHERE id_aluno = ?");
            if ($stmt_graduacao) {
                $stmt_graduacao->bind_param("i", $id_professor);
                $stmt_graduacao->execute();
                $graduacao_result = $stmt_graduacao->get_result();
                if ($graduacao_result->num_rows > 0) {
                    $professor_modalidades[$row['id']] = $graduacao_result->fetch_assoc()['faixa'];
                }
                $stmt_graduacao->close();
            }
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
    <title>Shaolin Piracicaba | Cadastro do Professor</title>
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
        <form class="w-75" action="cadastro_professor.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_professor); ?>">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($professor['nome']); ?>" required>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2 w-50">
                    <label for="usuario" class="form-label">Usuário</label>
                    <input type="text" class="input-field" name="usuario" id="usuario" autocomplete="off" maxlength="30" value="<?php echo htmlspecialchars($professor['usuario']); ?>" required>
                    <div id="usuario-error" class="form-error"></div>
                </div>
                <div class="mx-2" style="width: 30%">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="input-field" name="senha" id="senha" minlength="6" maxlength="30" autocomplete="off" placeholder="<?php echo $id_professor ? 'Deixe em branco para não alterar' : ''; ?>" <?php if (!$id_professor) echo 'required'; ?>>
                    <div id="senha-error" class="form-error"></div>
                </div>
                <div class="ms-2" style="width: 20%">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="input-field" name="telefone" id="telefone" autocomplete="off" maxlength="15" value="<?php echo htmlspecialchars($professor['telefone']); ?>" required>
                    <div id="telefone-error" class="form-error"></div>
                </div>
            </div>
            <div class="d-flex flex-direction-row mb-3">
                <div class="me-2" style="width: 88%">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" value="<?php echo htmlspecialchars($professor['email']); ?>" required>
                    <div id="email-error" class="form-error"></div>
                </div>
                <div class="ms-2 mt-5" style="width: 12%">
                    <div class="form-check">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" name="admin" id="admin" <?php if ($professor['admin']) echo 'checked'; ?>>
                        Administrador
                    </label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="modalidades" class="form-label">Modalidades</label>
                <?php foreach ($modalidades_disponiveis as $modalidade):
                    $id_modalidade = $modalidade['id'];
                    $nome_modalidade = htmlspecialchars($modalidade['nome']);
                    
                    $checked = isset($professor_modalidades[$id_modalidade]) ? 'checked' : '';
                    $graduacao_selecionada = $professor_modalidades[$id_modalidade] ?? 'Faixa/Estrela';
                    
                    $graduacoes = [];
                    if (stripos($nome_modalidade, 'shaolin do norte') !== false) {
                        $graduacoes = ['Faixa Branca', 'Faixa Amarela', 'Faixa Azul', 'Faixa Verde', 'Faixa Vermelha', 'Faixa Preta', 'Estrela Azul', 'Estrela Cinza', 'Estrela Preta', 'Estrela Azul Yin Yang', 'Estrela Cinza Yin Yang', 'Estrela Preta Yin Yang'];
                    } else if (stripos($nome_modalidade, 'shaolin kids') !== false) {
                        $graduacoes = ['Faixa Branca Risco Preto', 'Faixa Laranja', 'Faixa Amarela com Risco Preto', 'Faixa Roxa', 'Faixa Azul Risco Preto', 'Faixa Marrom', 'Faixa Verde Risco Preto', 'Faixa Verde'];
                    } else if (stripos($nome_modalidade, 'sanda') !== false) {
                        $graduacoes = ['Estrela com Contorno Prata e o Centro Preto', 'Estrela com Contorno Prata e o Centro Vermelho', 'Estrela Prata', 'Estrela com Contorno Dourado e o Centro Preto', 'Estrela Dourada com o Centro Vermelho', 'Estrela Dourada'];
                    }
                ?>
                <div class="d-flex flex-row w-100 justify-content-between my-2">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="modalidades[<?php echo $id_modalidade; ?>][selecionada]" value="1" <?php echo $checked; ?>>
                            <?php echo $nome_modalidade; ?>
                        </label>
                    </div>
                    
                    <?php if (!empty($graduacoes)): ?>
                    <div class="dropdown">
                        <input type="hidden" name="modalidades[<?php echo $id_modalidade; ?>][graduacao]" value="<?php echo htmlspecialchars($graduacao_selecionada); ?>">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            <?php echo htmlspecialchars($graduacao_selecionada); ?>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($graduacoes as $graduacao): ?>
                            <li><a class="dropdown-item" href="#"><?php echo htmlspecialchars($graduacao); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
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
