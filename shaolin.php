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
        <title>Shaolin Piracicaba | Shaolin do Norte</title>
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
                    <img src="https://ik.imagekit.io/shaolin/img/shaolin_banner.png?updatedAt=1762783987036" alt="Banner Shaolin do Norte">
                </div>

                <div class="mb-5">
                    <p class="mod_individual">
                        O Kung Fu Shaolin do Norte é um dos estilos mais tradicionais e influentes das artes marciais chinesas, 
                        reconhecido mundialmente por sua combinação de força, agilidade, equilíbrio e espiritualidade. Originário 
                        dos lendários Monges Shaolin, localizados na região norte da China, esse sistema reflete séculos de aperfeiçoamento 
                        técnico e filosófico, transmitido de geração em geração por monges guerreiros dedicados à busca do autodomínio.
                        <br>
                        <b>Origem e Filosofia:</b><br>
                        As raízes do Shaolin do Norte remontam ao Mosteiro Shaolin de Henan, considerado o berço do Kung Fu. 
                        Nesse templo, os monges desenvolveram técnicas de combate inspiradas nos movimentos dos animais e nas leis da natureza, 
                        unindo corpo, mente e espírito em uma prática voltada não apenas para a autodefesa, mas também para o aperfeiçoamento pessoal.
                        O treinamento enfatiza a disciplina, o respeito, a humildade e a superação constante, 
                        valores que moldam o caráter dos praticantes dentro e fora do tatame.
                        <br>
                        <b>Características Técnicas:</b> <br>
                        O estilo Shaolin do Norte é conhecido por seus movimentos amplos, posturas altas e baixas bem definidas, além
                        da fluidez entre ataque e defesa. É um sistema que valoriza a mobilidade e a velocidade, com 
                        ênfase em chutes poderosos, golpes longos e sequências complexas de formas (taolus).
                        Essas características demonstram a integração entre força e suavidade, combinando a energia interna (Qi) e a técnica 
                        externa (Gong Fu), resultando em uma arte marcial completa e harmoniosa.
                        <br>
                        <b>Treinamento e Benefícios:</b> <br>
                        A prática do Shaolin do Norte é completa e desafiadora. O treinamento desenvolve condicionamento físico, 
                        coordenação motora, concentração e equilíbrio emocional. Através de rotinas intensas, o praticante fortalece não 
                        apenas o corpo, mas também a mente, aprendendo a lidar com desafios, a manter a calma sob pressão e a cultivar o foco em cada movimento.
                        Com o tempo, o aluno compreende que o verdadeiro Kung Fu não é apenas a luta, mas a arte de viver com disciplina, respeito e harmonia.
                        <br>
                        <b>Legado:</b> <br>
                        O legado do Kung Fu Shaolin do Norte transcende as fronteiras da China. Ao longo dos séculos, 
                        influenciou inúmeros estilos de artes marciais e inspirou pessoas no mundo inteiro a buscar autoconhecimento, saúde e equilíbrio.
                        Na nossa academia, preservamos essa tradição milenar com orgulho, unindo o ensinamento técnico 
                        à filosofia que faz do Kung Fu uma arte marcial e espiritual completa.
                    </p>
                </div>

                <div class="horarios">
                    <h4 class="subtitulo"><b>Dias das Aulas</b></h4>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Segunda-Feira</strong><br>
                                8h às 9h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Segunda-Feira</strong><br>
                                17h às 18h e 19h às 20h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Terça-Feira</strong><br>
                                17h às 18h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Quarta-Feira</strong><br>
                                17h às 18h e 19h às 20h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Quinta-Feira</strong><br>
                                17h às 18h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Sexta-Feira</strong><br>
                                8h às 9h
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="aula_dias">
                                <strong>Sexta-Feira</strong><br>
                                17h às 18h
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <h4 class="subtitulo"><b>Mais Informações</b></h4>
                    <a href="https://api.whatsapp.com/send?phone=5519995194437&text=Ol%C3%A1%2C%20gostaria%20de%20saber%20mais%20informa%C3%A7%C3%B5es%20sobre%20o%20Shaolin%20do%20Norte%20e%20os%20valores%20das%20aulas%21" 
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
                                <div class="mods-item">
                                    <div class="img-placeholder">
                                        <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Tai Chi Chuan">
                                    </div>
                                    <p><h6>Tai Chi Chuan</h6></p>
                                    <a href="taichi.php" class="btn btn_verde">Saiba Mais</a>
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