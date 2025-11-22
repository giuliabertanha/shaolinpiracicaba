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
        <link rel="stylesheet" href="css/caroussel.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" href="img/icon.png" type="image/x-icon">
        <script src="js/moda.js" defer></script>
        <title>Shaolin Piracicaba | Modalidades</title>
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg p-0">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between">
                        <a class="navbar-brand" href="index.php">
                            <img class="m-2" id="logo_cabecalho" src="./img/logo.svg" alt="Logotipo">
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
        <main class="container-modalidades m-auto" width="85%">
            <h2 class="text-uppercase mt-4 text-center"><b>Modalidades</b></h2>
            <br>
            <div class="modalidade px-4 py-2 mx-5 my-2">
                <div class="content d-flex flex-direction-row">
                    <div class="caroussel-wrapper me-lg-4">
                            <button class="prev" data-caroussel="0">‹</button>
                        <div class="caroussel m-auto" data-caroussel="0">
                            <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Shaolin do Norte" class="active">
                            <img src="https://ik.imagekit.io/shaolin/img/shaolin_2.jpeg?updatedAt=1762482289496" alt="Shaolin do Norte">
                            <img src="https://ik.imagekit.io/shaolin/img/shaolin_3.jpeg?updatedAt=1762482289804" alt="Shaolin do Norte">
                        </div>
                        <button class="next" data-caroussel="0">›</button>
                    </div>
                    <div class="text-content">
                        <h2 class="titulo_mod">Shaolin do Norte</h2>
                        <p>O Shaolin do Norte é um tradicional estilo de Kung Fu originado dos mosteiros 
                            Shaolin situados no norte da China. Caracteriza-se por movimentos amplos, 
                            rápidos e poderosos, que enfatizam a força das pernas, a agilidade e a técnica de 
                            chutes, buscando a fluidez e a elegância dos movimentos, combinando 
                            defesa e ataque em harmonia. Além da parte física, o estilo também valoriza 
                            a disciplina mental e a meditação, formando não apenas guerreiros, mas praticantes 
                            com corpo, mente e espírito.
                        </p>
                        <br>
                        <div class="btn-container">
                            <a href="shaolin.php" class="btn btn_verde">Saiba Mais</a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="modalidade px-4 py-2 mx-5 my-2">
                <div class="content d-flex flex-direction-row">
                    <div class="caroussel-wrapper me-lg-4">
                            <button class="prev" data-caroussel="1">‹</button>
                        <div class="caroussel m-auto" data-caroussel="1">
                            <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Shaolin Kids" class="active">
                            <img src="https://ik.imagekit.io/shaolin/img/kids_2.jpeg?updatedAt=1762534055168" alt="Shaolin Kids">
                            <img src="https://ik.imagekit.io/shaolin/img/kids_3.jpeg?updatedAt=1762534055190" alt="Shaolin Kids">
                        </div>
                        <button class="next" data-caroussel="1">›</button>
                    </div>
                    <div class="text-content">
                        <h2 class="titulo_mod">Shaolin do Norte Kids</h2>
                        <p>O Shaolin do Norte é um estilo de Kung Fu que vem dos antigos monges 
                            Shaolin da China. Ele é conhecido por ter movimentos grandes, 
                            rápidos e cheios de energia! Nesse estilo, usamos muito as pernas 
                            e os chutes, além de aprender a ter equilíbrio, agilidade e concentração.
                            Nas aulas, as crianças aprendem não só a se movimentar como verdadeiros 
                            pequenos guerreiros, mas também a respeitar os colegas, ter disciplina 
                            e cuidar do corpo e da mente. O Shaolin do Norte é uma forma 
                            divertida e saudável de crescer forte, focado e com o coração tranquilo!
                        </p>
                        <div class="btn-container">
                            <a href="kids.php" class="btn btn_verde">Saiba Mais</a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="modalidade px-4 py-2 mx-5 my-2">
                <div class="content d-flex flex-direction-row">
                    <div class="caroussel-wrapper me-lg-4">
                            <button class="prev" data-caroussel="2">‹</button>
                        <div class="caroussel m-auto" data-caroussel="2">
                            <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Sanda" class="active">
                            <img src="https://ik.imagekit.io/shaolin/img/sanda_2.jpeg?updatedAt=1762482289651" alt="Sanda">
                            <img src="https://ik.imagekit.io/shaolin/img/sanda_3.jpeg?updatedAt=1762482289721" alt="Sanda">
                        </div>
                        <button class="next" data-caroussel="2">›</button>
                    </div>
                    <div class="text-content">
                        <h2 class="titulo_mod">Sanda</h2>
                        <p>O Sanda, também chamado de Boxe Chinês, é a forma de combate moderna 
                            do Kung Fu, desenvolvida a partir das antigas técnicas de defesa e ataque 
                            usadas pelos monges e guerreiros chineses. Ele combina socos, chutes, 
                            defesas, projeções e quedas, formando um sistema completo e dinâmico de luta.
                            Além do condicionamento físico, o Sanda trabalha reflexos, estratégia e controle 
                            emocional, promovendo o equilíbrio entre força e técnica. É uma arte marcial 
                            que valoriza não apenas o desempenho no combate, mas também o respeito, a 
                            disciplina e o autoconhecimento, pilares essenciais do verdadeiro espírito marcial.
                        </p>
                        <br>
                        <div class="btn-container">
                            <a href="sanda.php" class="btn btn_verde">Saiba Mais</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modalidade px-4 py-2 mx-5 my-2 mt-5">
                <div class="content d-flex flex-direction-row">
                    <div class="caroussel-wrapper me-lg-4">
                            <button class="prev" data-caroussel="3">‹</button>
                        <div class="caroussel m-auto" data-caroussel="3">
                            <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Tai Chi" class="active">
                            <img src="https://ik.imagekit.io/shaolin/img/tai_3.jpeg?updatedAt=1762534054975" alt="Tai Chi">
                            <img src="https://ik.imagekit.io/shaolin/img/tai_2.jpeg?updatedAt=1762534054490" alt="Tai Chi">
                        </div>
                        <button class="next" data-caroussel="3">›</button>
                    </div>
                    <div class="text-content">
                        <h2 class="titulo_mod">Tai Chi Chuan</h2>
                        <p>O Tai Chi Chuan é um estilo tradicional do Kung Fu interno, conhecido 
                            por seus movimentos lentos, suaves e contínuos. Ele combina arte marcial,   
                            meditação e exercícios de respiração, buscando o equilíbrio entre corpo e mente.
                            A prática do Tai Chi Chuan melhora a postura, a concentração, a flexibilidade 
                            e o controle da respiração, ajudando a reduzir o estresse e aumentar a 
                            energia vital. Mais do que uma arte marcial, o Tai Chi é um caminho de autoconhecimento 
                            e harmonia, que ensina o praticante a se mover com calma, foco e serenidade — 
                            dentro e fora do tatame.
                        </p>
                        
                        <div class="btn-container">
                            <a href="taichi.php" class="btn btn_verde">Saiba Mais</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <br>
        <footer class="d-flex justify-content-between align-items-center p-4">
            <span>&copy; 2025 Shaolin Kung Fu Piraciaba - Todos os direitos reservados.</span>
            <class="redes-sociais">
                <a href="https://api.whatsapp.com/send/?phone=5519995194437"><i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i></a>
                <a href="https://www.facebook.com/shaolinpiracicaba/?locale=pt_BR"><i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i></a>
                <a href="https://www.instagram.com/shaolinpiracicaba/"><i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i></a>
            </div>
        </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
    </body>
</html>