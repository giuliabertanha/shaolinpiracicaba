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
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <title>Shaolin Piracicaba | Tai Chi Chuan</title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg p-0">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between">
                        <a class="navbar-brand" href="index.php">
                            <img class="m-2" id="logo_cabecalho" src="https://ik.imagekit.io/shaolin/img/logo.svg?updatedAt=1762482367227" alt="Logotipo">
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
                                        <a class="nav-link dropdown-bs-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                        <a class="nav-link" href="login.php">Área do Aluno/Professor</a>
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

        <main class="container-modalidades">
            <div class="container-modalidades">
                <br>

                <div class="mb-4 banner">
                    <img src="https://ik.imagekit.io/shaolin/img/Tai%20_chi_banner.png?updatedAt=1762783987018" alt="Banner Tai Chi Chuan">
                </div>

                <div class="mb-5">
                    <p class="mod_individual">
                        O Tai Chi Chuan é uma arte marcial chinesa que une movimento, respiração e consciência em uma prática harmoniosa e 
                        profunda. Também conhecido como “a meditação em movimento”, o Tai Chi vai muito além da autodefesa: é uma 
                        filosofia de vida que busca o equilíbrio entre corpo, mente e espírito.
                        Originário da antiga China, o Tai Chi integra princípios do Taoismo e da medicina tradicional 
                        chinesa, valorizando o cultivo da energia vital — o Qi (Chi) — e a harmonia entre o Yin e o Yang, as forças complementares da natureza.
                        <br>
                        <b>Origem e Filosofia: </b> <br>
                        A história do Tai Chi Chuan remonta a séculos atrás, sendo atribuída a mestres taoístas que desenvolveram 
                        movimentos suaves e circulares inspirados nas leis do universo e nas formas da natureza.
                        O termo “Tai Chi” representa o supremo equilíbrio, e “Chuan” significa punho ou arte de combate — juntos, 
                        formam o conceito de uma arte marcial que busca a harmonia suprema através do movimento.
                        Seu ensinamento vai além da luta física: o Tai Chi é um caminho para o autoconhecimento, a serenidade e o fortalecimento interior.
                        <br>
                        <b>Características Técnicas: </b> <br>
                        O Tai Chi Chuan é caracterizado por movimentos lentos, contínuos e fluidos, executados com controle respiratório e concentração mental.
                        Apesar da suavidade aparente, cada gesto possui base marcial e aplicação prática, o que torna o Tai 
                        Chi um sistema completo de autodefesa e desenvolvimento interno.
                        Os movimentos são circulares e sem interrupções, promovendo o fluxo equilibrado da energia vital, 
                        enquanto a coordenação entre respiração e intenção traz clareza mental e estabilidade emocional.
                        Existem diferentes estilos de Tai Chi Chuan — como Chen, Yang, Wu e Sun —, cada um com suas particularidades, 
                        mas todos compartilham o mesmo propósito: o cultivo da energia interna e o equilíbrio entre ação e serenidade.
                        <br>
                        <b>Treinamento e Benefícios: </b> <br>
                        A prática do Tai Chi Chuan é acessível a pessoas de todas as idades e níveis de condicionamento, 
                        oferecendo benefícios tanto físicos quanto mentais.
                        Ela melhora a postura, o equilíbrio e a flexibilidade, reduz o estresse e a ansiedade, aumenta a 
                        energia vital e fortalece o sistema imunológico e respiratório.
                        Além disso, estimula a atenção plena (mindfulness) e o autodomínio, conduzindo o praticante a uma vida mais calma e equilibrada.
                        Com a prática constante, o aluno aprende a mover-se com consciência, transformando cada gesto em 
                        uma meditação em movimento e cada respiração em um passo rumo ao equilíbrio interior.
                        <br>
                        <b>Legado e Tradição: </b> <br>
                        O Tai Chi Chuan é reconhecido mundialmente como uma das mais belas expressões da cultura 
                        chinesa e uma poderosa ferramenta de bem-estar e autodesenvolvimento.
                        Na nossa academia, ensinamos o Tai Chi Chuan preservando sua essência tradicional, com ênfase na 
                        precisão técnica, na respiração consciente e na filosofia que inspira cada movimento.
                        Mais do que uma arte marcial, o Tai Chi é um caminho de vida — um convite à harmonia, à paz interior 
                        e à compreensão profunda de si mesmo e do mundo ao redor.
                    </p>
                </div>

                <div class="horarios">
                    <h4 class="subtitulo"><b>Dias das Aulas</b></h4>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Segunda-Feira</strong><br>
                                7h às 8h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Sexta-Feira</strong><br>
                                7h às 8h
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <h4 class="subtitulo"><b>Mais Informações</b></h4>
                    <a href="https://wa.me/5519995194437?
                        text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20Tai%20Chi%20Chuan%20e%20os%20valores%20das%20aulas!" 
                        target="_blank" class="btn_verde whatsApp">
                        <i class="fa-brands fa-whatsapp fa-xl"></i>
                        Para valores ou mais informações, clique aqui!
                    </a>
                </div>
                <section class="section" id="mods">
                    <h2 class="subtitulo">Outras Modalidades</h2>
                    <br>
                    <div class="mods-wrapper">
                        <div class="mods-viewport">
                            <div class="mods-track">
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Shaolin do Norte">
                                    </div>
                                    <p><h6>Shaolin do Norte</h6></p>
                                    <a href="shaolin.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Shaolin Kids">
                                    </div>
                                    <p><h6>Shaolin Kids</h6></p>
                                    <a href="kids.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Sanda">
                                    </div>
                                    <p><h6>Sanda</h6></p>
                                    <a href="sanda.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <br>

        <footer class="d-flex justify-content-between align-items-center p-4">
            <span>&copy; 2025 Shaolin Kung Fu Piracicaba - Todos os direitos reservados.</span>
            <div class="redes-sociais">
                <i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i>
                <i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i>
                <i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i>
            </div>
        </footer>
        <script src="js/bootstrap.bundle.min.js"></script>
    </body>
</html>