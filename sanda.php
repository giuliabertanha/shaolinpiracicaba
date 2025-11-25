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
    $stmt = $conn->prepare("SELECT id, nome, tipo FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result_user = $stmt->get_result();
    if ($user = $result_user->fetch_assoc()) {
        $user_id = $user['id'];
        $user_nome = $user['nome'];
        $tipo_usuario = $user['tipo'];
    }
    $stmt->close();
}

$area_usuario_link = ($tipo_usuario == 'P') ? 'area_professor.php' : 'area_aluno.php';
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
        <title>Shaolin Piracicaba | Sanda</title>
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
                                        <a class="nav-link" href="<?php if (isset($_SESSION['usuario'])) { echo $area_usuario_link; } else { echo 'login.php';}?>">Área do Aluno/Professor</a>
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
                    <img src="https://ik.imagekit.io/shaolin/img/sanda_banner.png?updatedAt=1762783986966" alt="Banner Sanda">
                </div>

                <div class="mb-5">
                    <p class="mod_individual">
                        O Wushu Sanda — também conhecido como Sanshou, que significa “mãos livres” — 
                        é a vertente de combate do Wushu moderno, desenvolvida a partir das técnicas tradicionais 
                        das artes marciais chinesas e adaptada para o contexto esportivo e competitivo.
                        O Sanda representa a expressão prática do Kung Fu, combinando golpes de punho, chutes, 
                        projeções e estratégias de combate em um sistema dinâmico, técnico e extremamente eficaz.
                        <br>
                        <b>Origem e Filosofia:</b> <br>
                        O Sanda surgiu como uma forma de treinamento militar e policial na China, reunindo 
                        técnicas de defesa pessoal derivadas de diversos estilos tradicionais de Kung Fu.
                        Com o tempo, foi sistematizado e transformado em uma modalidade esportiva reconhecida 
                        internacionalmente, que hoje faz parte das competições oficiais de Wushu moderno.
                        Mais do que apenas um esporte de contato, o Wushu Sanda mantém a filosofia marcial chinesa, 
                        valorizando o respeito, o autocontrole, a disciplina e o equilíbrio entre corpo e mente.
                        Cada luta é vista como uma oportunidade de crescimento pessoal e de aprimoramento técnico.
                        <br>
                        <b>Características Técnicas:</b> <br>
                        O Wushu Sanda se destaca pela variedade e eficiência das suas técnicas, combinando elementos de socos, chutes e quedas.
                        Ele é considerado uma das modalidades mais completas das artes marciais modernas, 
                        pois desenvolve tanto o ataque quanto a defesa em situações reais de combate.
                        Durante o combate, os atletas buscam pontuar com golpes limpos e eficientes, mantendo o domínio técnico e tático.
                        A combinação de velocidade, precisão e controle faz do Sanda um espetáculo de técnica e energia.
                        <br>
                        <b>Treinamento e Benefícios: </b> <br>
                        O treinamento de Wushu Sanda é completo e desafiador, envolvendo condicionamento físico, resistência, agilidade, reflexo e força mental.
                        Os praticantes aprendem a dominar o corpo e a mente, fortalecendo-se tanto física quanto emocionalmente.
                        Além da autodefesa, o Sanda proporciona melhora na concentração, na autoconfiança e no autocontrole, 
                        tornando-se uma ferramenta poderosa de desenvolvimento pessoal.
                        É uma modalidade que ensina não apenas a lutar, mas também a pensar estrategicamente e agir com equilíbrio em qualquer situação.
                        <br>
                        <b>Legado e Tradição: </b> <br>
                        Hoje, o Wushu Sanda é praticado em todo o mundo e reconhecido por sua eficácia, beleza e profundidade técnica.
                        Ele representa a evolução moderna do Kung Fu tradicional, preservando seus valores e adaptando-os à realidade esportiva contemporânea.
                        Na nossa academia, o Sanda é ensinado com respeito à tradição e foco na segurança, 
                        buscando formar atletas completos — fortes, ágeis e conscientes — que representam com orgulho o verdadeiro espírito das artes marciais chinesas.
                    </p>
                </div>

                <div class="horarios">
                    <h4 class="subtitulo"><b>Dias das Aulas</b></h4>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Segunda-Feira</strong><br>
                                18h às 19h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Terça-Feira</strong><br>
                                19h às 20h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Quarta-Feira</strong><br>
                                18h às 19h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Quinta-Feira</strong><br>
                                19h às 20h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Sexta-Feira</strong><br>
                                18h às 19h
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <h4 class="subtitulo"><b>Mais Informações</b></h4>
                    <a href="https://api.whatsapp.com/send?phone=5519995194437&text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20Sanda%20Boxe%20Chin%C3%AAs%20e%20os%20valores%20das%20aulas%21" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="btn_verde whatsApp">
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
                                        <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Tai Chi Chuan">
                                    </div>
                                    <p><h6>Tai Chi Chuan</h6></p>
                                    <a href="taichi.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Shaolin do Norte">
                                    </div>
                                    <p><h6>Shaolin do Norte</h6></p>
                                    <a href="shaolin.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Shaolin kids">
                                    </div>
                                    <p><h6>Shaolin Kids</h6></p>
                                    <a href="kids.php" class="btn btn_verde">Saiba Mais</a>
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