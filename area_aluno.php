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

if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'A') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesD.css"> 
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
        <div class="m-auto container-aluno">
            <h2 class="text-uppercase mb-5 text-center"><b>Área do Aluno</b></h2>
            <div class="student-header my-3">
                <span>Nome do Aluno</span>
                <span>Modalidade</span>
                <span>Faixa/Estrela</span>
            </div>
            <div class="d-flex justify-content-center">
                <a href="cadastro.php" class="btn btn_verde w-100 mx-0">MEU CADASTRO</a>
            </div>
            <section class="section mt-5" id="graduacao">
                <h2 class="text-center">GRADUAÇÃO POR FAIXA</h2>
                <div class="graduacao-item">
                    <h3>MODALIDADE X</h3>
                    <h4>Faixa 1</h4>
                    <ul>
                        <li>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</li>
                    </ul>
                </div>

                <div class="graduacao-item">
                        <h4>Próxima faixa: Faixa 2</h4>
                    <ul>
                        <li>Integer magna diam, lacinia et ornare sed, tempor vitae sapien.</li>
                    </ul>
                </div>
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