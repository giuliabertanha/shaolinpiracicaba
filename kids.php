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
        <title>Shaolin Piracicaba | Shaolin Kids</title>
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
                    <img src="https://ik.imagekit.io/shaolin/img/kids_banner.png?updatedAt=1762783986944" alt="Banner Shaolin Kids">
                </div>

                <div class="mb-5">
                    <p class="mod_individual">
                        O Shaolin do Norte é um dos estilos mais antigos e famosos do Kung Fu, conhecido por unir força, 
                        agilidade, equilíbrio e concentração. Ele nasceu há muitos séculos, com os monges do Templo 
                        Shaolin, na China, que treinavam o corpo e a mente para alcançar o autodomínio — ou seja, 
                        aprender a controlar a si mesmos com calma, foco e disciplina.
                        Na Turma Kids, as crianças aprendem essa arte milenar de um jeito divertido e educativo, com treinos 
                        que ajudam no crescimento físico e emocional.
                        <br>
                        <b>Origem e Valores:</b> <br>
                        Os monges Shaolin se inspiravam nos movimentos dos animais e na natureza, criando 
                        técnicas que fortalecem o corpo e o caráter. Aqui, seguimos esses mesmos princípios — 
                        as crianças aprendem que o Kung Fu não é só sobre lutar, mas também sobre respeito, paciência, cooperação e autoconfiança.
                        Cada movimento é uma lição de disciplina e superação, ensinando o aluno a se concentrar, persistir e acreditar no próprio potencial.
                        <br>
                        <b>Movimentos e Treino:</b> <br>
                        O Shaolin do Norte se destaca por seus movimentos amplos e dinâmicos, com muitos chutes, 
                        saltos e acrobacias. As crianças adoram praticar as formas (taolus), que parecem verdadeiras 
                        coreografias de luta, e também aprendem o uso básico de armas tradicionais como o bastão e a 
                        espada — sempre com total segurança e foco na coordenação motora.
                        Esse estilo trabalha o corpo todo, melhorando a força, flexibilidade, equilíbrio e concentração. 
                        A prática ajuda a gastar energia de forma positiva, melhora o rendimento escolar e estimula o espírito de equipe.
                        <br>
                        <b>Benefícios e Propósito:</b> <br>
                        Mais do que uma arte marcial, o Shaolin do Norte é uma escola de vida. Cada aula ensina a 
                        criança a ter autocontrole, respeito e confiança, valores que ela levará para casa, para a escola e para o futuro.
                        Com o tempo, os pequenos aprendem que o verdadeiro Kung Fu não está apenas nos golpes, mas 
                        na forma como enfrentamos os desafios do dia a dia com equilíbrio, coragem e alegria.
                        <br>
                        <b>Nosso Compromisso:</b> <br>
                        Na nossa academia, mantemos viva essa tradição milenar de forma lúdica e educativa, 
                        valorizando o desenvolvimento integral da criança — corpo, mente e espírito — em um ambiente acolhedor, seguro e cheio de aprendizado.
                     </p>
                </div>

                <div class="horarios">
                    <h2 class="subtitulo"><b>Dias das Aulas</b></h2>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Terça-Feira</strong><br>
                                18h às 19h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Quinta-Feira</strong><br>
                                18h às 19h
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <h2 class="subtitulo"><b>Mais Informações</b></h2>
                    <a href="https://wa.me/5519995194437?
                        text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20Shaolin%20Kids%20e%20os%20valores%20das%20aulas!" 
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
                                        <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Sanda">
                                    </div>
                                    <p><h6>Sanda</h6></p>
                                    <a href="sanda.php" class="btn btn_verde">Saiba Mais</a>
                                </div>
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