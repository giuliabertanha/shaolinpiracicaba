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
    <link rel="stylesheet" href="css/styles2.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="img/icon.png" type="image/x-icon">
    <title>Shaolin Piracicaba | Home</title>
    <style>
        /*3 itens lado a lado */
        @media (min-width: 768px) {
            .carousel-inner .carousel-item-end,
            .carousel-inner .carousel-item-start, 
            .carousel-inner .carousel-item-next,
            .carousel-inner .carousel-item-prev,
            .carousel-inner .carousel-item.active {
                display: flex;
                justify-content: center;
            }
        }
        
        /* Celular - esconde os 2 cards */
        @media (max-width: 767.98px) {
            .carousel-inner .carousel-item > div:not(:first-child) {
                display: none !important;
            }
        }

        .carousel-inner {
            padding: 0 3rem;
        }
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            opacity: 1;
        }
        .carousel-control-prev i,
        .carousel-control-next i {
            color: #161616 !important;
        }
        .btn-modalidade {
            width: 50% !important;
            padding: 0.375rem 0.75rem !important;
            margin-top: 0.5rem !important;
        }
    </style>
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
                                    <a class="nav-link active" aria-current="page" href="index.php">Home</a>
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
    
    <main>
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-11 col-xl-10"> 
                    
                    <h2 class="text-uppercase mt-4 mb-3 text-center"><b>HOME</b></h2>
                    <div class="ratio ratio-16x9" style="border-radius: 8px; overflow: hidden; background-color: #000;">
                        <video controls>
                            <source src="img/VidHome.mp4" type="video/mp4">
                            Seu navegador não suporta a tag de vídeo.
                        </video>
                    </div>

                    <h3 class="text-uppercase mt-5">Sobre o Kung-fu</h3>
                    <p>O Kung Fu é uma das artes marciais mais antigas e respeitadas do mundo, originária da China. Ao longo dos séculos, desenvolveu-se não apenas como uma forma de defesa pessoal, mas também como uma prática que busca o equilíbrio físico, mental e espiritual. O termo "Kung Fu" pode ser traduzido como "habilidade adquirida com esforço", refletindo a dedicação e disciplina necessárias para dominar os movimentos e princípios dessa arte.</p>
                    <p>O Kung Fu é composto por uma série de técnicas que incluem socos, chutes, defesas, armas e posturas que ajudam a fortalecer o corpo e a mente. Além disso, ele valoriza o autoconhecimento e a harmonia entre o praticante e o ambiente ao seu redor. Em nossa escola, buscamos não apenas a excelência técnica, mas também o desenvolvimento pessoal, ensinando valores como disciplina, coragem humildade, respeito, e honra. Seja você iniciante ou praticante avançado, o Kung Fu é uma jornada de autodescoberta e evolução, capaz de transformar a sua vida em muitos aspectos.</p>

                    <h3 class="text-uppercase mt-5 mb-5 text-center">Modalidades</h3>

                    <div id="modalidadeCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <!-- Slide 1: Shaolin do Norte, Kids, Sanda -->
                            <div class="carousel-item active">
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Modalidade Shaolin do Norte" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Shaolin do Norte</h5>
                                    <a href="shaolin.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Modalidade Kids" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Kids</h5>
                                    <a href="kids.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Modalidade Sanda" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Sanda</h5>
                                    <a href="sanda.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                            </div>

                            <!-- Slide 2: Tai Chi Chuan, Shaolin do Norte, Kids -->
                            <div class="carousel-item">
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Modalidade Tai Chi Chuan" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Tai Chi Chuan</h5>
                                    <a href="taichi.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Modalidade Shaolin do Norte" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Shaolin do Norte</h5>
                                    <a href="shaolin.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Modalidade Kids" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Kids</h5>
                                    <a href="kids.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                            </div>

                            <!-- Slide 3: Sanda, Tai Chi Chuan, Shaolin do Norte -->
                            <div class="carousel-item">
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Modalidade Sanda" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Sanda</h5>
                                    <a href="sanda.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Modalidade Tai Chi Chuan" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Tai Chi Chuan</h5>
                                    <a href="taichi.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/shaolin_1.jpeg?updatedAt=1762439710926" alt="Modalidade Shaolin do Norte" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Shaolin do Norte</h5>
                                    <a href="shaolin.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                            </div>

                            <!-- Slide 4: Kids, Sanda, Tai Chi Chuan -->
                            <div class="carousel-item">
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/kids.jpeg?updatedAt=1762439710834" alt="Modalidade Kids" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Kids</h5>
                                    <a href="kids.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/sanda.jpeg?updatedAt=1762439710935" alt="Modalidade Sanda" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Sanda</h5>
                                    <a href="sanda.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                                <div class="text-center mx-2 p-3" style="width: 100%; display: inline-block;">
                                    <div style="width: 150px; height: 150px; border-radius: 10px; padding: 0; overflow: hidden; display: inline-block; margin-bottom: 15px;">
                                        <img src="https://ik.imagekit.io/shaolin/img/tai.jpeg?updatedAt=1762482289684" alt="Modalidade Tai Chi Chuan" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <h5 class="mt-2">Tai Chi Chuan</h5>
                                    <a href="taichi.php" class="btn btn_verde btn-modalidade mx-auto">Saiba Mais</a>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#modalidadeCarousel" data-bs-slide="prev">
                            <i class="fa-solid fa-chevron-left fa-3x"></i>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#modalidadeCarousel" data-bs-slide="next">
                            <i class="fa-solid fa-chevron-right fa-3x"></i>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>

                    <div class="mt-5 mb-4" style="background-color: #f0f0f0; border-radius: 8px; overflow: hidden;">
                        <div class="row align-items-stretch">
                            <div class="col-md-6 p-5"> 
                                <h3 class="text-uppercase mb-4 fw-bold">CONTATO</h3>
                                <ul class="list-unstyled" style="line-height: 3;"> 
                                    <li class="d-flex align-items-center">
                                        <a href="https://api.whatsapp.com/send/?phone=5519995194437" target="_blank">
                <i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i>
             </i> 19 99519-4437</a>
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <a class="me-1" href="https://www.instagram.com/shaolinpiracicaba/" target="_blank">
                <i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i>
            </a>@shaolinpiracicaba
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <a  class="me-1" href="https://www.facebook.com/shaolinpiracicaba/?locale=pt_BR" target="_blank">
                <i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i>
            </a> Shaolin Piracicaba Kung Fu
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <i class="fa-solid fa-location-dot fa-lg me-2 ms-2" style="width: 20px;"></i>
                                        <a href="https://www.google.com/maps/search/?api=1&query=R.+Treze+de+Maio,+474+-+Centro,+Piracicaba+-+SP,+13400-300" target="_blank" class="link-contato">
                                        R. Treze de Maio, 474-Centro, Piracicaba-SP, 13400-300
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6 p-0"> 
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3680.175263236719!2d-47.6503037!3d-22.7217262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94c631140158602b%3A0x57c9966f5793b871!2sShaolin%20Kung%20Fu%20Piracicaba!5e0!3m2!1spt-BR!2sbr!4v1762823981417!5m2!1spt-BR!2sbr" width="100%" height="100%" style="border:0; display: block; min-height: 400px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div> 
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
    
</body>
</html>