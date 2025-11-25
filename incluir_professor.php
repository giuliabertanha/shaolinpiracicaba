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
    echo "<script>alert('Essa página exige acesso com um usuário administrador.'); window.location.href = 'cadastro_professores.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $admin = isset($_POST['admin']) ? 1 : 0;
    $modalidades_selecionadas = $_POST['modalidades'] ?? [];

    $alguma_modalidade_selecionada = false;
    foreach ($modalidades_selecionadas as $id_modalidade => $dados) {
        if (isset($dados['selecionada'])) {
            // Verifica se a modalidade tem graduações
            $stmt_check_grad = $conn->prepare("SELECT COUNT(*) as total FROM graduacoes WHERE id_modalidade = ?");
            $stmt_check_grad->bind_param("i", $id_modalidade);
            $stmt_check_grad->execute();
            $result_check = $stmt_check_grad->get_result();
            $row_check = $result_check->fetch_assoc();
            $stmt_check_grad->close();
            
            // Se tem graduações, precisa ter uma selecionada
            if ($row_check['total'] > 0 && empty($dados['id_graduacao'])) {
                continue; // Pula esta modalidade
            }
            
            $alguma_modalidade_selecionada = true;
            break;
        }
    }

    if (!$alguma_modalidade_selecionada) {
        echo "<script>alert('É obrigatório selecionar e definir a graduação para ao menos uma modalidade.'); window.history.back();</script>";
        exit;
    }

    //Verifica se o usuário já existe
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
        // Insere o novo professor
        $stmt_insert = $conn->prepare("INSERT INTO usuarios (usuario, senha, nome, telefone, email, tipo, admin, emb_ab, emb_5anos, emb_camp) VALUES (?, ?, ?, ?, ?, 'P', ?, 0, 0, 0)");
        $stmt_insert->bind_param("sssssi", $usuario, $senha_hash, $nome, $telefone, $email, $admin);
        $stmt_insert->execute();
        $id_novo_professor = $conn->insert_id;
        $stmt_insert->close();

        // Insere as matrículas
        foreach ($modalidades_selecionadas as $id_modalidade => $dados) {
            if (isset($dados['selecionada'])) {
                // Se a modalidade tem graduação, insere a matrícula
                if (!empty($dados['id_graduacao'])) {
                    $id_graduacao = intval($dados['id_graduacao']);

                    // Insere matrícula
                    $stmt_matricula = $conn->prepare("INSERT INTO matriculas (id_usuario, id_modalidade, id_graduacao) VALUES (?, ?, ?)");
                    $stmt_matricula->bind_param("iii", $id_novo_professor, $id_modalidade, $id_graduacao);
                    $stmt_matricula->execute();
                    $stmt_matricula->close();
                }

                $stmt_find_slot = $conn->prepare("SELECT id_professor1, id_professor2, id_professor3, id_professor4 FROM modalidades WHERE id = ?");
                $stmt_find_slot->bind_param("i", $id_modalidade);
                $stmt_find_slot->execute();
                $professores_modalidade = $stmt_find_slot->get_result()->fetch_assoc();
                $stmt_find_slot->close();

                $slot_vago = null;
                for ($i = 1; $i <= 4; $i++) {
                    if (is_null($professores_modalidade['id_professor' . $i])) {
                        $slot_vago = 'id_professor' . $i;
                        break;
                    }
                }

                if ($slot_vago) {
                    $stmt_update_mod = $conn->prepare("UPDATE modalidades SET $slot_vago = ? WHERE id = ?");
                    $stmt_update_mod->bind_param("ii", $id_novo_professor, $id_modalidade);
                    $stmt_update_mod->execute();
                    $stmt_update_mod->close();
                } else {
                    throw new Exception("A modalidade selecionada já possui o número máximo de 4 professores.");
                }
            }
        }
        $conn->commit();
        echo "<script>alert('Professor cadastrado com sucesso!'); window.location.href = 'cadastro_professores.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Erro ao cadastrar professor: " . $e->getMessage() . "'); window.history.back();</script>";
    }
    exit;
}

$sql_modalidades = "SELECT id, nome FROM modalidades ORDER BY nome";
$result_modalidades = $conn->query($sql_modalidades);
$modalidades_disponiveis = [];
if ($result_modalidades && $result_modalidades->num_rows > 0) {
    while($row = $result_modalidades->fetch_assoc()) {
        $stmt_graduacoes = $conn->prepare("SELECT id, nome FROM graduacoes WHERE id_modalidade = ? ORDER BY ordem");
        $stmt_graduacoes->bind_param("i", $row['id']);
        $stmt_graduacoes->execute();
        $modalidades_disponiveis[] = ['modalidade' => $row, 'graduacoes' => $stmt_graduacoes->get_result()->fetch_all(MYSQLI_ASSOC)];
    }
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
    <title>Shaolin Piracicaba | Incluir Professor</title>
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
    <main class="d-flex flex-column align-items-center pt-2">
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir professor</b></h2>  
        <form class="w-75" action="incluir_professor.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" maxlength="60" required>
                <div id="nome-error" class="form-error"></div>
            </div>
            <div class="div-form">
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
            <div class="div-form">
                <div class="me-2" style="width: 88%">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="input-field" name="email" id="email" autocomplete="off" maxlength="60" required>
                    <div id="email-error" class="form-error"></div>
                </div>
                <div class="ms-2 check-admin" style="width: 12%">
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
                <?php foreach ($modalidades_disponiveis as $item) {
                    $id_modalidade = $item['modalidade']['id'];
                    $nome_modalidade = htmlspecialchars($item['modalidade']['nome']);
                    $graduacoes = $item['graduacoes'];
                ?>
                <div class="d-flex flex-row w-100 justify-content-between my-2 div-form-modalidades">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="modalidades[<?php echo $id_modalidade; ?>][selecionada]" value="1">
                            <?php echo $nome_modalidade; ?>
                        </label>
                    </div>
                    
                    <?php if (!empty($graduacoes)){ ?>
                    <div class="dropdown">
                        <input type="hidden" name="modalidades[<?php echo $id_modalidade; ?>][id_graduacao]" value="">
                        <button class="dropdown-bs-toggle btn btn-light dropdown-faixa" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            Faixa/Estrela
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($graduacoes as $graduacao) { ?>
                            <li><a class="dropdown-item" href="#" data-grad-id="<?php echo $graduacao['id']; ?>"><?php echo htmlspecialchars($graduacao['nome']); ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
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

                    // Atualiza o botão e o input hidden com o ID da graduação selecionada
                    const dropdownItems = linha.querySelectorAll('.dropdown-item');
                    dropdownItems.forEach(item => {
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            const nomeGraduacao = this.textContent;
                            const idGraduacao = this.getAttribute('data-grad-id');
                            if (botaoDropdown && inputOculto) {
                                botaoDropdown.textContent = nomeGraduacao;
                                inputOculto.value = idGraduacao;
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

                    // Só valida se: modalidade marcada E tem dropdown (tem graduações) E não selecionou graduação
                    if (caixaSelecao && caixaSelecao.checked && botaoDropdown && botaoDropdown.textContent.trim() === 'Faixa/Estrela') {
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