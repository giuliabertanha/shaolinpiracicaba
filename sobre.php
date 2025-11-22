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
    <title>Shaolin Kung Fu Piracicaba</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/styles2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
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

        @media (min-width: 768px) and (max-width: 1200px) {
            .carousel-image-container {
                margin: 0 1rem !important;
            }
        }

        @media (max-width: 768px) {
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

        .carousel-image-container {
            width: 250px;
            height: 250px;
            border-radius: 10px;
            padding: 0;
            overflow: hidden;
            margin: 0 auto 15px auto;
            display: block; 
        }
        .carousel-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
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
                                    <a class="nav-link active" aria-current="page" href="sobre.php">Sobre</a>
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

    <main>
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-11 col-xl-10">

                    <section class="sobre d-flex flex-column align-items-center">
                        <h2 class="text-uppercase mt-4 mb-3 text-center"><b>Sobre</b></h2>
                        
                        <div class="row align-items-center">
                            <div class="col-lg-7 texto-sobre">
                                <p>O professor Paulo Medeiros é praticante dedicado e professor de Kung Fu, com anos de experiência no estudo e no ensino das artes marciais chinesas. Iniciou sua jornada, buscando aprimoramento físico e equilíbrio mental, e ao longo do tempo, transformou essa paixão em um caminho de vida.</p>
                                <p>Formado em Educação Física pela UNIMEP e nos estilos Shaolin do Norte, Sanda e Shuaijiao atua como professor na Escola Shaolin e técnico da Seleção Brasileira de Kung Fu Wushu (CBKW) no departamento de Shuaijiao. desenvolveu profundo conhecimento nas técnicas tradicionais e nos princípios filosóficos que fundamentam o verdadeiro Kung Fu.</p>
                                <p>Com uma metodologia que une disciplina, respeito e motivação, o Professor Paulo Medeiros busca transmitir a cada aluno não apenas a técnica, mas também os valores que fazem do Kung Fu uma arte de autoconhecimento e superação.</p>
                                <p>Participou de diversos cursos, seminários, campeonatos nacionais e internacionais, representando nossa cidade e nosso país, mantendo assim o compromisso de aprimorar-se e preservar a essência do Kung Fu tradicional.</p>
                            </div>
                            <div class="col-lg-5 text-center my-4 my-lg-0">
                                <img class="rounded-4 img-fluid" style="max-width: 330px;" src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/PHOTO-2025-11-06-12-11-07.jpg?updatedAt=1762442674110" alt="Professor Paulo Medeiros">
                            </div>
                        </div>
                    </section>

                    <section class="estrutura d-flex flex-column align-items-center mt-5">
                        <h2 class="mb-3">ESTRUTURA</h2>
                        <p>Nossa academia foi planejada para proporcionar conforto, segurança e o ambiente ideal para o aprendizado e a prática do Kung Fu. Contamos com um espaço amplo, arejado e cuidadosamente organizado, oferecendo todas as condições necessárias para treinos de qualidade e bem-estar dos alunos.</p>
                        <p>O salão principal possui piso adequado para atividades marciais, espelhos para correção de postura e iluminação que garante uma atmosfera harmoniosa e inspiradora. Também dispomos de equipamentos de apoio, como sacos de pancada, colchonetes, aparadores, escudos e armas tradicionais chinesas, utilizados nos treinos técnicos e nas práticas avançadas.</p>
                    </section>

                    <section>
                        <div id="estruturaCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-wrap="true">
                            <div class="carousel-inner">
                                <div class="carousel-item">
                                    <div class="col-md-4 fade-in d-flex justify-content-center">
                                        <div class="carousel-image-container">
                                            <img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_123533145.png?updatedAt=1762443368660" alt="Estrutura da academia 1">
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item active">
                                    <div class="col-md-4 fade-in d-flex justify-content-center"> 
                                        <div class="carousel-image-container">
                                            <img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_122735615.png?updatedAt=1762442860709" alt="Estrutura da academia 2">
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="col-md-4 fade-in d-flex justify-content-center">
                                        <div class="carousel-image-container">
                                            <img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_122753428.png?updatedAt=1762442901531" alt="Estrutura da academia 3">
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="col-md-4 fade-in d-flex justify-content-center">
                                        <div class="carousel-image-container">
                                            <img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_122812495.png?updatedAt=1762442901764" alt="Estrutura da academia 4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#estruturaCarousel" data-bs-slide="prev">
                                <i class="fa-solid fa-chevron-left fa-3x"></i>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#estruturaCarousel" data-bs-slide="next">
                                <i class="fa-solid fa-chevron-right fa-3x"></i>
                                <span class="visually-hidden">Próximo</span>
                            </button>
                        </div>
                    </section>
                    
                    <section class="estrutura d-flex flex-column">
                        <p>Além disso, nossa estrutura conta com banheiros, área de recepção, espaço para alongamento e fortalecimento, onde os alunos e familiares podem interagir e acompanhar as aulas. Tudo é pensado para unir tradição e conforto, criando um ambiente que estimula o foco, a disciplina e o crescimento pessoal.</p>
                        <p>Aqui, cada detalhe foi projetado para que o praticante vivencie o verdadeiro espírito do Kung Fu — com segurança, respeito e energia positiva.</p>
                    </section>
                    
                    <section class="parceiros mb-4">
                        <h2 class="text-center">PARCEIROS</h2> <div class="logos d-flex justify-content-center flex-wrap mt-3">
                            <div class="parceiro"><img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_122944344.png?updatedAt=1762443025289" alt="Logo Parceiro 1"></div>
                            <div class="parceiro"><img src="img/gbc.jpg" alt="Logo Parceiro 2"></div>
                            <div class="parceiro"><img src="img/avec.png" alt="Logo Parceiro 3"></div>
                            <div class="parceiro"><img src="https://ik.imagekit.io/shaolinpic/Sobre_Shaolin/imagem_2025-11-06_123009966.png?updatedAt=1762443024481" alt="Logo Parceiro 4"></div>
                            <div class="parceiro"><img src="img/nineyards.jpeg" alt="Logo Parceiro 5"></div>
                            <div class="parceiro"><img src="img/mcr.jpeg" alt="Logo Parceiro 6"></div>
                            <div class="parceiro"><img src="img/nutricesta.png" alt="Logo Parceiro 7"></div>
                        </div>
                    </section>

                </div></div></div></main>

    <footer class="d-flex justify-content-between align-items-center p-4">
        <span>&copy; 2025 Shaolin Kung Fu Piracicaba - Todos os direitos reservados.</span>
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

        let items = document.querySelectorAll('#estruturaCarousel .carousel-item')

        items.forEach((el) => {
            const minPerSlide = 3
            let next = el.nextElementSibling
            for (var i=1; i<minPerSlide; i++) {
                if (!next) {
                    next = items[0]
                }
                let cloneChild = next.querySelector('div').cloneNode(true)
                el.appendChild(cloneChild)
                next = next.nextElementSibling
            }
        })
    </script>
</body>
</html>