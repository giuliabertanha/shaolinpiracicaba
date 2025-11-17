<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} 

if (!isset($_POST['login'])) {
    header("Location: login.php");
    exit();
}

//Formatando o nome da modalidade e criar um nome de tabela válido
function formatar_nome_tabela($nome, $conn) {
    $nome_sem_acentos = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    // Converte para minúsculas
    $nome_minusculo = strtolower($nome_sem_acentos);
    $nome_tabela = preg_replace('/[^a-z0-9_]+/', '_', $nome_minusculo);
    $nome_tabela = trim($nome_tabela, '_');
    return mysqli_real_escape_string($conn, $nome_tabela);
}

$sql_professores = "SELECT id, nome FROM usuarios WHERE tipo = 'P' ORDER BY nome";
$result_professores = $conn->query($sql_professores);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $professores_selecionados = $_POST['professores'] ?? [];
    $faixas = $_POST['faixas'] ?? [];

    // Validação do número de professores
    if (count($professores_selecionados) > 4) {
        echo "<script>alert('Você só pode selecionar no máximo 4 professores.'); window.history.back();</script>";
        exit;
    }

    $id_professor1 = isset($professores_selecionados[0]) ? (int)$professores_selecionados[0] : null;
    $id_professor2 = isset($professores_selecionados[1]) ? (int)$professores_selecionados[1] : null;
    $id_professor3 = isset($professores_selecionados[2]) ? (int)$professores_selecionados[2] : null;
    $id_professor4 = isset($professores_selecionados[3]) ? (int)$professores_selecionados[3] : null;

    //Verifica se já existe uma modalidade com o mesmo nome
    $stmt_check = $conn->prepare("SELECT id FROM modalidades WHERE nome = ?");
    $stmt_check->bind_param("s", $nome);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->close();
        echo "<script>alert('Já existe uma modalidade com este nome. Por favor, escolha outro.'); window.history.back();</script>";
        exit;
    }
    $stmt_check->close();

    $stmt = $conn->prepare("INSERT INTO modalidades (nome, id_professor1, id_professor2, id_professor3, id_professor4) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("siiii", $nome, $id_professor1, $id_professor2, $id_professor3, $id_professor4);

    if ($stmt->execute()) {
        $novo_id = $conn->insert_id;

        $nome_tabela = formatar_nome_tabela($nome, $conn);
        $sql_create_table = "CREATE TABLE `$nome_tabela` (
            id INT PRIMARY KEY AUTO_INCREMENT,
            id_aluno INT,
            faixa CHAR(30),
            id_professor INT,
            id_mod INT,
            FOREIGN KEY (id_aluno) REFERENCES usuarios(id),
            FOREIGN KEY (id_professor) REFERENCES usuarios(id),
            FOREIGN KEY (id_mod) REFERENCES modalidades(id)
        )";
        if ($conn->query($sql_create_table)) {
            // Inserir as faixas/estrelas na tabela 'graduacoes'
            if (!empty($faixas)) {
                $stmt_faixa = $conn->prepare("INSERT INTO graduacoes (id_modalidade, nome, ordem) VALUES (?, ?, ?)");
                $ordem = 1;
                foreach ($faixas as $faixa_nome) {
                    $faixa_nome_trim = trim($faixa_nome);
                    if (!empty($faixa_nome_trim)) {
                        $stmt_faixa->bind_param("isi", $novo_id, $faixa_nome_trim, $ordem);
                        $stmt_faixa->execute();
                        $ordem++;
                    }
                }
                $stmt_faixa->close();
            }

            echo "<script>alert('Modalidade e tabela criadas com sucesso!'); window.location.href = 'cadastro_modalidades.php';</script>";
        } else {
            echo "<script>alert('Modalidade criada, mas houve um erro ao criar a tabela: " . $conn->error . "'); window.location.href = 'cadastro_modalidades.php';</script>";
        }
    } else {
        echo "<script>alert('Erro ao inserir dados: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    $conn->close();
    exit;
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Incluir modalidade</b></h2>  
        <form class="w-75" action="incluir_modalidade.php" method="POST">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="input-field" name="nome" id="nome" autocomplete="off" required>
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
            <div class="mb-3">
                <label class="form-label">Faixas/Estrelas</label>
                <div id="faixas-container">
                    <input type="text" class="form-control mb-2" name="faixas[]" placeholder="Nome da Faixa/Estrela">
                </div>
                <button type="button" id="add-faixa" class="btn btn-cinza mx-0 btn-sm mt-2 mb-0">Adicionar Faixa/Estrela</button>
            </div>
            <div class="d-flex w-100 mt-4 mb-5">
                <button type="submit" class="btn text-uppercase w-50 ms-0 btn_verde">Salvar</button>
                <a href="cadastro_modalidades.php" class="btn text-uppercase w-50 me-0 voltar">Voltar</a>
            </div>
        </form>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Bloqueia a seleção de mais de  professores
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="professores[]"]');

            function updateCheckboxState() {
                const checkedCount = document.querySelectorAll('input[name="professores[]"]:checked').length;

                if (checkedCount >= 4) {
                    checkboxes.forEach(cb => {
                        if (!cb.checked) {
                            cb.disabled = true;
                        }
                    });
                } else {
                    checkboxes.forEach(cb => {
                        cb.disabled = false;
                    });
                }
            }
            checkboxes.forEach(checkbox => checkbox.addEventListener('change', updateCheckboxState));
            updateCheckboxState(); // Executa ao carregar a página

            //Adiciona campos de faixa/estrela dinamicamente
            document.getElementById('add-faixa').addEventListener('click', function() {
                const container = document.getElementById('faixas-container');
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.className = 'form-control mb-2';
                newInput.name = 'faixas[]';
                newInput.placeholder = 'Nome da Faixa/Estrela';
                container.appendChild(newInput);
            });
        });
    </script>
</body>
</html>
