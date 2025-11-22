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

// Primeiro, verifica se o usuário está logado. Se não, redireciona para o login.
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$admin_logado = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$proprio_perfil = isset($_SESSION['id']) && isset($_GET['id']) && $_SESSION['id'] == $_GET['id'];

if (!$admin_logado && !$proprio_perfil) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_professor_post = $_POST['id'] ?? null;

    if (isset($_POST['excluir']) && !empty($id_professor_post)) {
        $conn->begin_transaction();
        try {
            $stmt_update_mod1 = $conn->prepare("UPDATE modalidades SET id_professor1 = NULL WHERE id_professor1 = ?");
            $stmt_update_mod1->bind_param("i", $id_professor_post);
            $stmt_update_mod1->execute();
            $stmt_update_mod1->close();

            $stmt_update_mod2 = $conn->prepare("UPDATE modalidades SET id_professor2 = NULL WHERE id_professor2 = ?");
            $stmt_update_mod2->bind_param("i", $id_professor_post);
            $stmt_update_mod2->execute();
            $stmt_update_mod2->close();

            $stmt_update_mod3 = $conn->prepare("UPDATE modalidades SET id_professor3 = NULL WHERE id_professor3 = ?");
            $stmt_update_mod3->bind_param("i", $id_professor_post);
            $stmt_update_mod3->execute();
            $stmt_update_mod3->close();

            $stmt_update_mod4 = $conn->prepare("UPDATE modalidades SET id_professor4 = NULL WHERE id_professor4 = ?");
            $stmt_update_mod4->bind_param("i", $id_professor_post);
            $stmt_update_mod4->execute();
            $stmt_update_mod4->close();

            $stmt_delete_user = $conn->prepare("DELETE FROM usuarios WHERE id = ? AND tipo = 'P'");
            $stmt_delete_user->bind_param("i", $id_professor_post);
            $stmt_delete_user->execute();
            $stmt_delete_user->close();

            $conn->commit();
            echo "<script>alert('Professor excluído com sucesso!'); window.location.href = 'cadastro_professores.php';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Erro ao excluir professor: " . $e->getMessage() . "'); window.history.back();</script>";
        }
        $conn->close();
        exit;
    }

    if (isset($_POST['id']) && !empty($_POST['id']) && !isset($_POST['excluir'])) {
        $id_professor_update = $_POST['id'];
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];
        $admin = isset($_POST['admin']) ? 1 : 0;
        $modalidades_post = $_POST['modalidades'] ?? [];

        $conn->begin_transaction();
        try {
            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_update_user = $conn->prepare("UPDATE usuarios SET nome = ?, usuario = ?, senha = ?, telefone = ?, email = ?, admin = ? WHERE id = ?");
                $stmt_update_user->bind_param("sssssii", $nome, $usuario, $senha_hash, $telefone, $email, $admin, $id_professor_update);
            } else {
                $stmt_update_user = $conn->prepare("UPDATE usuarios SET nome = ?, usuario = ?, telefone = ?, email = ?, admin = ? WHERE id = ?");
                $stmt_update_user->bind_param("ssssii", $nome, $usuario, $telefone, $email, $admin, $id_professor_update);
            }
            $stmt_update_user->execute();
            $stmt_update_user->close();

            $stmt_mod_atuais = $conn->prepare("SELECT id FROM modalidades WHERE id_professor1 = ? OR id_professor2 = ? OR id_professor3 = ? OR id_professor4 = ?");
            $stmt_mod_atuais->bind_param("iiii", $id_professor_update, $id_professor_update, $id_professor_update, $id_professor_update);
            $stmt_mod_atuais->execute();
            $result_mod_atuais = $stmt_mod_atuais->get_result();
            $modalidades_atuais_ids = [];
            while ($row = $result_mod_atuais->fetch_assoc()) {
                $modalidades_atuais_ids[] = $row['id'];
            }
            $stmt_mod_atuais->close();

            $modalidades_selecionadas_ids = array_keys($modalidades_post);

            $modalidades_para_remover = array_diff($modalidades_atuais_ids, $modalidades_selecionadas_ids);
            foreach ($modalidades_para_remover as $id_mod_remover) {
                for ($i = 1; $i <= 4; $i++) {
                    $stmt_remove = $conn->prepare("UPDATE modalidades SET id_professor$i = NULL WHERE id = ? AND id_professor$i = ?");
                    $stmt_remove->bind_param("ii", $id_mod_remover, $id_professor_update);
                    $stmt_remove->execute();
                    $stmt_remove->close();
                }
            }

            $stmt_delete_matriculas = $conn->prepare("DELETE FROM matriculas WHERE id_usuario = ?");
            $stmt_delete_matriculas->bind_param("i", $id_professor_update);
            $stmt_delete_matriculas->execute();
            $stmt_delete_matriculas->close();

            foreach ($modalidades_post as $id_modalidade => $dados) {
                if (isset($dados['selecionada']) && !empty($dados['graduacao'])) {
                    $stmt_grad = $conn->prepare("SELECT id FROM graduacoes WHERE nome = ? AND id_modalidade = ?");
                    $stmt_grad->bind_param("si", $dados['graduacao'], $id_modalidade);
                    $stmt_grad->execute();
                    $result_grad = $stmt_grad->get_result();
                    if ($grad_row = $result_grad->fetch_assoc()) {
                        $id_graduacao = $grad_row['id'];
                        $stmt_matricula = $conn->prepare("INSERT INTO matriculas (id_usuario, id_modalidade, id_graduacao) VALUES (?, ?, ?)");
                        $stmt_matricula->bind_param("iii", $id_professor_update, $id_modalidade, $id_graduacao);
                        $stmt_matricula->execute();
                        $stmt_matricula->close();
                    }
                    $stmt_grad->close();

                    if (!in_array($id_modalidade, $modalidades_atuais_ids)) {
                        $slot_vago = null;
                        for ($i = 1; $i <= 4; $i++) {
                            $check_slot = $conn->query("SELECT id_professor$i FROM modalidades WHERE id = $id_modalidade")->fetch_assoc();
                            if (is_null($check_slot['id_professor'.$i])) {
                                $slot_vago = 'id_professor' . $i;
                                break;
                            }
                        }
                        if ($slot_vago) {
                            $stmt_update_mod = $conn->prepare("UPDATE modalidades SET $slot_vago = ? WHERE id = ?");
                            $stmt_update_mod->bind_param("ii", $id_professor_update, $id_modalidade);
                            $stmt_update_mod->execute();
                            $stmt_update_mod->close();
                        } else {
                            throw new Exception("A modalidade '{$dados['nome']}' já possui o número máximo de 4 professores.");
                        }
                    }
                }
            }

            $conn->commit();
            echo "<script>alert('Professor atualizado com sucesso!'); window.location.href = 'cadastro_professor.php?id=" . $id_professor_update . "';</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Erro ao atualizar professor: " . $e->getMessage() . "'); window.history.back();</script>";
        }
        exit;
    }
}

$id_professor = null;
$professor = [
    'nome' => '',
    'usuario' => '',
    'telefone' => '',
    'email' => '',
    'admin' => ''
];

$professor_modalidades = [];
$professor_graduacoes = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_professor = $_GET['id'];

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

    $stmt_mod_prof = $conn->prepare("SELECT id FROM modalidades WHERE id_professor1 = ? OR id_professor2 = ? OR id_professor3 = ? OR id_professor4 = ?");
    $stmt_mod_prof->bind_param("iiii", $id_professor, $id_professor, $id_professor, $id_professor);
    $stmt_mod_prof->execute();
    $result_mod_prof = $stmt_mod_prof->get_result();
    while ($row_mod = $result_mod_prof->fetch_assoc()) {
        $professor_modalidades[] = $row_mod['id'];
    }
    $stmt_mod_prof->close();

    $stmt_grad_prof = $conn->prepare(
        "SELECT matriculas.id_modalidade, graduacoes.nome AS graduacao_nome 
         FROM matriculas 
         JOIN graduacoes ON matriculas.id_graduacao = graduacoes.id 
         WHERE matriculas.id_usuario = ?");
    $stmt_grad_prof->bind_param("i", $id_professor);
    $stmt_grad_prof->execute();
    $result_grad_prof = $stmt_grad_prof->get_result();
    while ($row_grad = $result_grad_prof->fetch_assoc()) {
        $professor_graduacoes[$row_grad['id_modalidade']] = $row_grad['graduacao_nome'];
    }
    $stmt_grad_prof->close();
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
    <title>Shaolin Piracicaba | Cadastro do Professor</title>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Cadastro do professor</b></h2>  
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
                <?php foreach ($modalidades_disponiveis as $item) {
                    $id_modalidade = $item['modalidade']['id'];
                    $nome_modalidade = htmlspecialchars($item['modalidade']['nome']);
                    $graduacoes = $item['graduacoes'];
                    $is_checked = in_array($id_modalidade, $professor_modalidades);
                    $graduacao_selecionada = $professor_graduacoes[$id_modalidade] ?? 'Faixa/Estrela';
                ?>
                <div class="d-flex flex-row w-100 justify-content-between my-2">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="modalidades[<?php echo $id_modalidade; ?>][selecionada]" value="1" <?php if ($is_checked) echo 'checked'; ?>>
                            <?php echo $nome_modalidade; ?>
                        </label>
                    </div>
                    
                    <?php if (!empty($graduacoes)){ ?>
                    <div class="dropdown">
                        <input type="hidden" name="modalidades[<?php echo $id_modalidade; ?>][graduacao]" value="<?php echo htmlspecialchars($graduacao_selecionada !== 'Faixa/Estrela' ? $graduacao_selecionada : ''); ?>">
                        <button class="dropdown-bs-toggle btn btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:400px;">
                            <?php echo htmlspecialchars($graduacao_selecionada); ?>
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
                <a href="cadastro_professores.php" class="btn text-uppercase w-50 voltar">Voltar</a>
                <?php if ($id_professor) { ?>
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
                        event.preventDefault();
                    }
                });
            }


        });
    </script>
</body>
</html>
