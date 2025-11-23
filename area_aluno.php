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
} else if ($_SESSION['tipo'] != 'A') {
    echo "<script>alert('Essa página exige acesso com um usuário de aluno.'); window.location.href = 'login.php';</script>";
}

$id_usuario = $_SESSION['id'];
$tipo_usuario = $_SESSION['tipo'];
$area_usuario_link = ($tipo_usuario == 'P') ? 'area_professor.php' : 'area_aluno.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario_post = $_POST['id'] ?? null;

    if ($id_usuario_post != $id_usuario) {
        echo "<script>alert('Acesso negado.'); window.location.href = '$area_usuario_link';</script>";
        exit;
    }

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $nome = $_POST['nome'];
        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];
        $telefone = $_POST['telefone'];
        $email = $_POST['email'];

        $conn->begin_transaction();
        try {
            if (!empty($senha)) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt_update_user = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                $stmt_update_user->bind_param("si", $senha_hash, $id_usuario);
                $stmt_update_user->execute();
                $stmt_update_user->close();
                $conn->commit();
                echo "<script>alert('Sua senha foi atualizada com sucesso!'); window.location.href = 'meu_cadastro.php';</script>";
            } else {
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

$stmt = $conn->prepare("SELECT nome, usuario, telefone, email, emb_ab, emb_5anos, emb_camp FROM usuarios WHERE id = ? AND tipo = 'A'");
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

$sql_modalidades = "SELECT
	        GROUP_CONCAT(modalidades.nome SEPARATOR ', ') AS 'Modalidade' 
        FROM matriculas 
            INNER JOIN usuarios ON (matriculas.id_usuario = ?)
            INNER JOIN modalidades ON (modalidades.id=matriculas.id_modalidade) 
            WHERE usuarios.tipo = 'A'
            GROUP BY usuarios.id;";
$stmt_modalidades = $conn->prepare($sql_modalidades);
$stmt_modalidades->bind_param("i", $id_usuario);
$stmt_modalidades->execute();
$result_modalidades = $stmt_modalidades->get_result();

$user_id = null;
$user_nome = '';
if (isset($_SESSION['usuario'])) {
    $stmt = $conn->prepare("SELECT id, nome, emb_ab, emb_5anos, emb_camp FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $user_id = $user['id'];
        $user_nome = $user['nome'];
        $emb_ab = $user['emb_ab'];
        $emb_5anos = $user['emb_5anos'];
        $emb_camp = $user['emb_camp'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles2.css"> 
    <link rel="stylesheet" href="css/styles.css"> 
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="img/icon.png" type="image/x-icon">
    <title>Shaolin Piracicaba | Área do Aluno</title> 
    <style>
        .container-aluno {
            width: 70%;
        }

        @media (max-width: 767.98px) {
            .container-aluno {
                width: 100%;
            }

            .loja-item {
                margin-left: 1rem;
            }
        }
    </style>
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
                                <li class="nav-item">
                                    <a class="nav-link" href="modalidades.html">Modalidades</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="sobre.html">Sobre</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="premiacoes.html">Premiações</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" aria-current="page"  href="area_aluno.php">Área do Aluno</a>
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
        <div class="m-auto container-aluno">
            <h2 class="text-uppercase mb-5 text-center"><b>Área do Aluno</b></h2>
            <div class="student-header my-3">
                <span><?php echo htmlspecialchars($user_nome); ?></span>
                <?php
                    echo '<span>' . htmlspecialchars($result_modalidades->fetch_assoc()["Modalidade"]) . '</span>';
                ?>
            </div>
            <div class="d-flex justify-content-center">
                <a href="meu_cadastro.php" class="btn btn_verde w-100 mx-0">MEU CADASTRO</a>
            </div>
            <section class="section d-flex aling-center justify-content-center mt-3" id="emblemas">
                <?php
                    if ($emb_ab == 1) {
                        echo '<img class="w-25" src="img/emblema_abertura.jpg" alt="Abertura completa">';
                    } else {
                        echo '<img class="w-25" src="img/emblema_abertura_pb.jpg">';
                    } 

                    if ($emb_5anos == 1) {
                        echo '<img class="w-25" src="img/emblema_5anos.jpg" alt="5 anos de treino">';
                    } else {
                        echo '<img class="w-25" src="img/emblema_5anos_pb.jpg">';
                    }

                    if ($emb_camp == 1) {
                        echo '<img class="w-25 ms-2" src="img/emblema_campeonato.jpg" alt="Medalhista campeonato">';
                    } else {
                        echo '<img class="w-25 ms-2" src="img/emblema_campeonato_pb.jpg" alt="Medalhista campeonato">';
                    }
                ?>
            </section>
            <section class="section mt-3" id="graduacao">
                <h2 class="text-center">GRADUAÇÃO POR FAIXA</h2>
                <?php if ($result_matriculas->num_rows > 0) { ?>
                    <?php while($matricula = $result_matriculas->fetch_assoc()) { ?>
                        <div class="detalhes_modalidade mt-5" style="text-align: left; column-count: 2; column-gap: 40px;">
                            <h4 class="text-uppercase"><?php echo htmlspecialchars($matricula['modalidade_nome']); ?></h4>
                            <?php
                                if (htmlspecialchars($matricula['modalidade_nome']) == 'Shaolin do Norte') {
                                    echo '<b>Primeira Fase - Faixa Branca</b>
                                        <ul>
                                            <li>Lian Bu Quan - Rotina Treina Passos</li>
                                            <li>Gun Fa - Técnicas de Bastão</li>
                                            <li>Shi Er Lu Tan Tui - 1</li>
                                        </ul>
                                        <b>Segunda Fase - Faixa Amarela</b>
                                        <ul>    
                                            <li>Duan Da Quan - Rotina Ataque Curto</li>
                                            <li>Qi Mei Gun - Bastão Altura da Sobrancelha</li>
                                            <li>Shi Er Lu Tan Tui - 2</li>
                                        </ul>
                                        <b>Terceira Fase - Faixa Azul</b>
                                        <ul>
                                            <li>Mei Hua Quan - Rotina Flor de Ameixa</li>
                                            <li>Dan Dao Ji Ben Gong - Fundamentos de Facão</li>
                                            <li>Pi Gua Dao - Facão Pi Gua</li>
                                            <li>Shi Er Lu Tan Tui - 3 e 4</li>
                                        </ul>
                                        <b>Quarta Fase - Faixa Verde</b>
                                        <ul>
                                            <li>Chuan Xin Quan - Rotina Atravessa Coração</li>
                                            <li>Beng Bu Quan - Rotina Passos Esmagadores (Louva-a-Deus)</li>
                                            <li>Long Xing Jian - Espada Forma do Dragão</li>
                                            <li>Shi Er Lu Tan Tui - 5 e 6</li>
                                        </ul>
                                        <b>Quinta Fase - Faixa Vermelha</b>
                                        <ul>
                                            <li>Wu Yi Quan - Rotina Habilidade Marcial</li>
                                            <li>Ti Lan Qiang - Lança Erguida com Bloqueio</li>
                                            <li>Shaolin Gun Dui Chai - Combinado Bastão Shaolin</li>
                                            <li>Shi Er Lu Tan Tui - 7, 8 e 9</li>
                                        </ul>
                                        <b>Sexta Fase - Faixa Preta</b>
                                        <ul>
                                            <li>Ba Bu Quan - Rotina Puxa Passos</li>
                                            <li>Ci Hu Bi - Punhal Espeta Tigre</li>
                                            <li>Jie Beng Bu - Combinado Passos Esmagadores</li>
                                            <li>Shi Er Lu Tan Tui - 10, 11 e 12</li>
                                        </ul>
                                        <b>Sétima Fase - Estrela Azul</b>
                                        <ul>
                                            <li>Zuo Ma Quan - Rotina Montar Cavalo</li>
                                            <li>Lian Hua Tiao Jian Quan - Flor de Lotus Salta o Riacho</li>
                                            <li>Jin Gang Quan - Punho Vajra</li>
                                            <li>Liu He Dao - Facão Seis Harmonias</li>
                                        </ul>
                                        <b>Oitava Fase - Estrela Cinza</b>
                                        <ul>
                                            <li>Ling Lu Quan - Rotina Apontar Caminho</li>
                                            <li>Di Tang Shuang Bi Shou - Punhal Duplo de Solo</li>
                                            <li>Ba Wang Quan - Punhos de Tirano (Cai Li Fo)</li>
                                            <li>Chun Qiu Da Dao - Facão Primavera Outono</li>
                                        </ul>
                                        <b>Nona Fase - Estrela Preta</b>
                                        <ul>
                                            <li>Kai Men Quan - Rotina Abrir Portões</li>
                                            <li>Bei Shaolin Dan Dao Jin Qiang - Combinado Shaolin do Norte de Facão contra Lança</li>
                                            <li>Sheng Ping Quan - Punhos da Prosperidade (Cha Quan)</li>
                                            <li>Di Tang Shuang Dao - Facão Duplo de Solo</li>
                                        </ul>
                                        <b>Décima Fase - Estrela Azul Yin Yang</b>
                                        <ul>
                                            <li>Lian Huan Quan - Rotina Continuidade</li>
                                            <li>Di Tang San Jie Gun - Bastão 3 Seções de Solo</li>
                                            <li>Yue Ya Chan - Pa Lua Crescente</li>
                                            <li>Shi Ba Shou - 18 mãos (Louva-a-Deus)</li>
                                        </ul>
                                        <b>Décima Primeira Fase - Estrela Cinza Yin Yang</b>
                                        <ul>
                                            <li>Fa Shi Quan - Rotina Ritos</li>
                                            <li>Hu Tou Shuang Gou - Gancho Duplo Cabeça de Tigre</li>
                                            <li>Di Tang Jiu Jie Bian - Corrente Nove Elos de Solo</li>
                                            <li>Duo Gang Quan - Evitar a Rigidez (Louva-a-Deus)</li>
                                        </ul>
                                        <b>Décima Segunda Fase - Estrela Preta Yin Yang</b>
                                        <ul>
                                            <li>Katis Especiais</li>
                                        </ul>';
                                } else if (htmlspecialchars($matricula['modalidade_nome']) == 'Shaolin Kids') {
                                    echo '<b>Primeira Fase - Faixa Branca Risco Preto</b>
                                        <ul>
                                            <li>Lian Bu Quan - Rotina Treina Passos até 25</li>
                                            <li>Socos 1, 2 e 3</li>
                                            <li>Chutes 1, 2 e 3</li>
                                            <li>Passo do Tigre 1 e 5</li>
                                            <li>Luta de Bastão</li>
                                        </ul>
                                        <b>Segunda Fase - Faixa Laranja</b>
                                        <ul>
                                            <li>Lian Bu Quan - Rotina Treina Passos até 50</li>
                                            <li>Gun Fa - Técnicas de Bastão</li>
                                            <li>Socos 4 e Sequência de Socos</li>
                                            <li>Chutes 4 e 5</li>
                                            <li>Passo do Tigre 2 e 3</li>
                                            <li>Batimento de Braço Individual</li>
                                        </ul>
                                        <b>Terceira Fase - Faixa Amarela com Risco Preto</b>
                                        <ul>
                                            <li>Duan Da Quan - Rotina Ataque Curto até 40</li>
                                            <li>Qi Mei Gun - Bastão Altura da Sobrancelha até 20</li>
                                            <li>Chutes 6, 7 e 8</li>
                                            <li>Passo do Tigre 4</li>
                                            <li>Batimento de Braço Individual</li>
                                        </ul>
                                        <b>Quarta Fase - Faixa Roxa</b>
                                        <ul>
                                            <li>Duan Da Quan - Rotina Ataque Curto até 70</li>
                                            <li>Qi Mei Gun - Bastão Altura da Sobrancelha até 30</li>
                                            <li>Chutes 9 e 10</li>
                                            <li>Batimento de Braço em Dupla</li>
                                        </ul>
                                        <b>Quinta Fase - Faixa Azul Risco Preto</b>
                                        <ul>
                                            <li>Mei Hua Quan - Rotina Flor de Ameixa</li>
                                            <li>Shi Er Lu Tan Tui - 1 e 2</li>
                                            <li>Passo do Tigre 6 e 7</li>
                                            <li>Batimento de Braço Individual</li>
                                        </ul>
                                        <b>Sexta Fase - Faixa Marrom</b>
                                        <ul>
                                            <li>Pi Gua Dao - Facão Pi Gua</li>
                                            <li>Shi Er Lu Tan Tui - 3 e 4</li>
                                            <li>Dan Dao Ji Ben Gong - Fundamentos do Facão</li>
                                            <li>Passo do Tigre 8</li>
                                            <li>Batimento de Braço em Dupla</li>
                                        </ul>
                                        <b>Sétima Fase - Faixa Verde Risco Preto</b>
                                        <ul>
                                            <li>Chuan Xin Quan - Rotina Atravessa Coração</li>
                                            <li>Shi Er Lu Tan Tui - 5</li>
                                            <li>Long Xing Jian - Espada Forma de Dragão até 30</li>
                                        </ul>
                                        <b>Oitava Fase - Faixa Verde</b>
                                        <ul>
                                            <li>Beng Bu Quan - Rotina Passos Esmagadores (Louva-a-Deus)</li>
                                            <li>Shi Er Lu Tan Tui - 6</li>
                                            <li>Long Xing Jian - Espada Forma de Dragão até 55</li>
                                        </ul>';
                                } else if (htmlspecialchars($matricula['modalidade_nome']) == 'Sanda') {
                                    echo "<b>Programa do Primeiro Nível - Estrela com o Contorno Prata e o Centro Preto</b>
                                        <ul>
                                            <li>Período mínimo necessário de prática: 2 meses</li>
                                            <li><b>Passadas:</b>
                                                <ul>
                                                    <li>1.01 - Avançando</li>
                                                    <li>1.02 - Recuando</li>
                                                </ul>
                                            </li>
                                            <li><b>Membros Superiores:</b>
                                                <ul>
                                                    <li>1.03 - Jab</li>
                                                    <li>1.04 - Dois Jab's</li>
                                                    <li>1.05 - Direto</li>
                                                </ul>
                                            </li>
                                            <li><b>Abertura Dinâmica:</b>
                                                <ul>
                                                    <li>1.06 - Levantamento - Frontal (perna reta)</li>
                                                    <li>1.07 - Levantamento - Circular para dentro (perna reta)</li>
                                                    <li>1.08 - Levantamento - Circular para fora (perna reta)</li>
                                                </ul>
                                            </li>
                                            <li><b>Membros Inferiores:</b>
                                                <ul>
                                                    <li>1.09 - Na coxa, chute circular, na parte lateral (fora), com a perna que está atrás</li>
                                                    <li>1.10 - Na coxa, chute circular, na parte medial (dentro), com a perna que está na frente</li>
                                                    <li>1.11 - Na cabeça, chute frontal com a perna que está atrás</li>
                                                    <li>1.12 - No abdômen, escora frontal com a perna que está na frente</li>
                                                </ul>
                                            </li>
                                            <li><b>Combinações com Membros Superiores:</b>
                                                <ul>
                                                    <li>1.13 - Jab e Direto</li>
                                                    <li>1.14 - Dois jab's e direto</li>
                                                </ul>
                                            </li>
                                            <li><b>Combinação com Membros Superiores e Inferiores:</b>
                                                <ul>
                                                    <li>1.15 - Jab e chute circular na parte lateral (fora) da coxa, com a perna que está atrás</li>
                                                    <li>1.16 - Direto e chute circular na parte medial (dentro) da coxa, com a perna que está na frente</li>
                                                    <li>1.17 - Jab e chute circular nas costelas, com a perna que está atrás</li>
                                                    <li>1.18 - Direto e chute circular nas costelas, com a perna que está na frente</li>
                                                </ul>
                                            </li>
                                            <li><b>Defesas:</b>
                                                <ul>
                                                    <li>1.19 - Esquivas (esquivas laterais contra jabs e diretos), para esquerda e direita</li>
                                                </ul>
                                            </li>
                                            <li><b>Amortecimentos:</b>
                                                <ul>
                                                    <li>1.20 - Frontal (decúbito ventral)</li>
                                                    <li>1.21 - Costas (decúbito dorsal)</li>
                                                    <li>1.22 - Lateral</li>
                                                </ul>
                                            </li>
                                            <li><b>Rolamentos:</b>
                                                <ul>
                                                    <li>1.23 - Para frente</li>
                                                    <li>1.24 - Na diagonal para frente</li>
                                                    <li>1.25 - Na diagonal para trás</li>
                                                </ul>
                                            </li>
                                            <li><b>Projeção 01:</b>
                                                <ul>
                                                    <li>1.26 - Single leg/projeção de uma perna</li>
                                                </ul>
                                            </li>
                                        </ul>

                                        <b>Programa do Segundo Nível - Estrela com o Contorno Prata e o Centro Vermelho</b>
                                        <ul>
                                            <li>Período mínimo necessário de prática: + 4 meses</li>
                                            <li><b>Passadas:</b>
                                                <ul>
                                                    <li>2.01 - Na lateral, deslocamento com a perna que está atrás</li>
                                                    <li>2.02 - Na lateral, deslocamento com a perna que está na frente</li>
                                                    <li>2.03 - Deslocamento na Lateral para Fora</li>
                                                    <li>2.04 - Deslocamento na Lateral para Dentro</li>
                                                    <li>2.05 - Deslocamento em Círculo</li>
                                                    <li>2.06 - Trocando de Base</li>
                                                </ul>
                                            </li>
                                            <li><b>Membros Superiores:</b>
                                                <ul>
                                                    <li>2.07 - Cruzado com o braço que está na frente</li>
                                                    <li>2.08 - Swing</li>
                                                    <li>2.09 - Upper com o braço que está na frente</li>
                                                    <li>2.10 - Upper com o braço que está atrás</li>
                                                </ul>
                                            </li>
                                            <li><b>Membros Inferiores:</b>
                                                <ul>
                                                    <li>2.11 - Na cabeça, chute circular com a perna que está atrás</li>
                                                    <li>2.12 - Na cabeça, chute circular com a perna que está na frente</li>
                                                    <li>2.13 - Escora lateral, com a perna que está na frente</li>
                                                </ul>
                                            </li>
                                            <li><b>Combinações com Membros Superiores:</b>
                                                <ul>
                                                    <li>2.14 - Cruzado com o braço que está na frente, seguido de direto</li>
                                                    <li>2.15 - Direto, seguido de cruzado com o braço que está na frente</li>
                                                    <li>2.16 - Jab, cruzado com o braço que está na frente, seguido de direto. Upper com o braço que está na frente</li>
                                                    <li>2.17 - Direto, seguido de cruzado com o braço que está na frente. Upper com o braço que está atrás</li>
                                                    <li>2.18 - Jab, seguido de direto. Cruzado com o braço que está na frente, seguido de swing</li>
                                                </ul>
                                            </li>
                                            <li><b>Combinações com Membros Superiores e Inferiores:</b>
                                                <ul>
                                                    <li>2.19 - Na cabeça, Jab, seguido de chute circular com a perna que está atrás</li>
                                                    <li>2.20 - Na cabeça, direto, seguido de chute circular com a perna que está na frente</li>
                                                    <li>2.21 - Na coxa, na parte lateral (fora), chute circular com a perna que está atrás, voltando à base de origem. Na cabeça, chute circular com a mesma perna, retornando à posição inicial</li>
                                                </ul>
                                            </li>
                                            <li><b>Defesa:</b>
                                                <ul>
                                                    <li>2.22 - Dois pêndulos</li>
                                                    <li>2.23 - Defesas de braços na linha lateral da cabeça</li>
                                                </ul>
                                            </li>
                                            <li><b>Projeção 02:</b>
                                                <ul>
                                                    <li>2.24 - Double leg/projeção de duas pernas para o lado</li>
                                                </ul>
                                            </li>
                                            <li><b>Antecipação 01:</b>
                                                <ul>
                                                    <li>2.25 - Antecipação com direto, seguido de single leg/projeção de 1 perna (projeção 3)</li>
                                                </ul>
                                            </li>
                                            <li><b>Resistência Específica:</b>
                                                <ul>
                                                    <li>Dois rounds de 2 minutos por um de descanso. Usando o critério de 15 segundos ativos por 10 segundos passivos, no saco de pancadas</li>
                                                </ul>
                                            </li>
                                        </ul>
                                    ";
                                }
                            ?>
                        </div>
                    <?php } ?>
                    <?php $stmt_matriculas->close(); ?>
                <?php } else { ?>
                    <p>Nenhuma modalidade encontrada.</p>
                <?php } ?>
            </section>

            <section class="section mt-3" id="apostila">
                <h2 class="text-center my-4">APOSTILA</h2>
                <div class="d-flex justify-content-center">
                    <a href="apostila.pdf" class="btn btn_verde w-100" download>
                        Clique aqui para fazer o download da apostila
                    </a>
                </div>
            </section>

            <section class="section my-4" id="agenda">
                <h2 class="text-center my-4">AGENDA</h2>
                <div class="agenda-box">
                    <strong>TABELA DE DIAS E HORÁRIOS DAS AULAS</strong>
                </div>
            </section>

            <section class="section mt-5" id="loja">
                <h2 class="text-center my-4">LOJA</h2>
                <div class="loja-wrapper">
                    <button class="arrow prev p-0" id="prev-btn">‹</button>
                    <div class="loja-viewport my-5">
                        <div class="loja-track">
                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Bermuda_Shaolin.jpeg?updatedAt=1763143188001" alt="Bermuda" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Bermuda." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Cal%C3%A7a_Shaolin.jpeg?updatedAt=1763143187811" alt="Calça" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Calça." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Camiseta_Shaolin.jpeg?updatedAt=1763143187797" alt="Camisa" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Camisa." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Faixa_Branca_Shaolin.jpeg?updatedAt=1763143187806" alt="Faixa" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Faixa." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>
                
                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Bermuda_Shaolin.jpeg?updatedAt=1763143188001" alt="Bermuda" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Bermuda%20Shaolin." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Regata_Sanda.jpeg?updatedAt=1763143187740" alt="Regata" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20a%20Regata." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Shorts_Sanda.jpeg?updatedAt=1763143187932" alt="Shorts" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20o%20Shorts." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                            <div class="loja-item mb-3">
                                <img src="https://ik.imagekit.io/shaolin/img/Leque.jpeg?updatedAt=1763143187678" alt="Leque" class="loja-item-img">
                                <a href="https://wa.me/5519995194437?text=Olá!%20Gostaria%20de%20comprar%20o%20Leque." 
                                    target="_blank" class="btn btn_verde">Comprar</a>
                            </div>

                        </div>
                    </div>
                    <button class="arrow next p-0" id="next-btn">›</button>
                </div>
            </section>
</main>
<footer class="d-flex justify-content-between align-items-center p-4" style="background-color: #f0f4f9;">
        <span>© 2025 Shaolin Kung Fu Piracicaba - Todos os direitos reservados.</span>
        <div class="redes-sociais">
            <a href="https://api.whatsapp.com/send/?phone=5519995194437" target="_blank">
                <i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i>
            </a>

            <a href="https://www.facebook.com/shaolinpiracicaba/?locale=pt_BR" target="_blank">
                <i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i>
            </a>

            <a href="https://www.instagram.com/shaolinpiracicaba/" target="_blank">
                <i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i>
            </a>
        </div>
    </footer>

    <script src="js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const track = document.querySelector('.loja-track');
            if (!track) return; 
            const items = Array.from(track.children);
            const nextButton = document.getElementById('next-btn');
            const prevButton = document.getElementById('prev-btn');
            const viewport = document.querySelector('.loja-viewport'); 
            if (items.length === 0 || !nextButton || !prevButton || !viewport) {
                return;
            }

            const itemWidth = items[0].getBoundingClientRect().width;
            const gap = parseInt(getComputedStyle(track).gap) || 0;
            const slideWidth = itemWidth + gap;
            const itemsVisible = Math.floor(viewport.clientWidth / slideWidth);
            const maxPosition = - (slideWidth * (items.length - itemsVisible));
            let currentPosition = 0;

            function moveToPosition(position) {
                track.style.transform = `translateX(${position}px)`;
                updateButtons();
            }

            function updateButtons() {
                prevButton.style.display = (currentPosition === 0) ? 'none' : 'block';
                const maxPosThreshold = maxPosition + (itemsVisible > 1 ? slideWidth / 2 : 0);
                nextButton.style.display = (currentPosition <= maxPosThreshold) ? 'none' : 'block';
            }

            nextButton.addEventListener('click', () => {
                currentPosition -= slideWidth;
                if (currentPosition < maxPosition) {
                    currentPosition = maxPosition;
                }
                moveToPosition(currentPosition);
            });

            prevButton.addEventListener('click', () => {
                currentPosition += slideWidth;
                if (currentPosition > 0) {
                    currentPosition = 0;
                }
                moveToPosition(currentPosition);
            });

            updateButtons();
            
            window.addEventListener('resize', () => {
                const newItemWidth = items[0].getBoundingClientRect().width;
                const newGap = parseInt(getComputedStyle(track).gap) || 0;
                const newSlideWidth = newItemWidth + newGap;
                const newItemsVisible = Math.floor(viewport.clientWidth / newSlideWidth);
                const newMaxPosition = - (newSlideWidth * (items.length - newItemsVisible));
                
                currentPosition = 0;
                moveToPosition(currentPosition);
            });
        });
    </script>
</body>
</html>