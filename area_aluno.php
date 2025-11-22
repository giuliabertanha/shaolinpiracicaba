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

$user_id = null;
$user_nome = '';
if (isset($_SESSION['usuario'])) {
    $stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $_SESSION['usuario']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $user_id = $user['id'];
        $user_nome = $user['nome'];
    }
    $stmt->close();
}

$graduacoes = [];
if ($user_id) {
    $sql = "
        SELECT
            m.nome AS modalidade_nome,
            f_atual.nome AS faixa_atual_nome,
            cf_atual.descricao AS conteudo_atual,
            f_proxima.nome AS proxima_faixa_nome,
            cf_proxima.descricao AS conteudo_proximo
        FROM matriculas ma
        JOIN modalidades m ON ma.modalidade_id = m.id
        JOIN faixas f_atual ON ma.faixa_id = f_atual.id
        LEFT JOIN conteudo_faixa cf_atual ON cf_atual.faixa_id = f_atual.id
        LEFT JOIN faixas f_proxima ON f_proxima.modalidade_id = m.id AND f_proxima.ordem = f_atual.ordem + 1
        LEFT JOIN conteudo_faixa cf_proxima ON cf_proxima.faixa_id = f_proxima.id
        WHERE ma.aluno_id = ?
        ORDER BY m.nome;
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $graduacoes_raw = [];
    while ($row = $result->fetch_assoc()) {
        $modalidade = $row['modalidade_nome'];
        if (!isset($graduacoes_raw[$modalidade])) {
            $graduacoes_raw[$modalidade] = [
                'faixa_atual_nome' => $row['faixa_atual_nome'],
                'conteudo_atual' => [],
                'proxima_faixa_nome' => $row['proxima_faixa_nome'],
                'conteudo_proximo' => []
            ];
        }
        if ($row['conteudo_atual']) {
            $graduacoes_raw[$modalidade]['conteudo_atual'][] = $row['conteudo_atual'];
        }
        if ($row['conteudo_proximo']) {
            $graduacoes_raw[$modalidade]['conteudo_proximo'][] = $row['conteudo_proximo'];
        }
    }
    $stmt->close();

    // Limpa duplicatas de conteúdo
    foreach ($graduacoes_raw as $modalidade => $details) {
        $graduacoes[$modalidade] = $details;
        $graduacoes[$modalidade]['conteudo_atual'] = array_unique($details['conteudo_atual']);
        $graduacoes[$modalidade]['conteudo_proximo'] = array_unique($details['conteudo_proximo']);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles2.css"> 
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
                                    <a class="nav-link" href="sobre.php">Sobre</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="premiacoes.php">Premiações</a>
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
            <div class="d-flex justify-content-center">
                <a href="meu_cadastro.php" class="btn btn_verde w-100 mx-0">MEU CADASTRO</a>
            </div>
            <section class="section mt-5" id="graduacao">
                <h2 class="text-center">GRADUAÇÃO POR FAIXA</h2>
                <?php if (empty($graduacoes)): ?>
                    <div class="graduacao-item text-center">
                        <p>Você ainda não está matriculado em nenhuma modalidade ou sua graduação não foi registrada.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($graduacoes as $modalidade => $graduacao): ?>
                        <div class="graduacao-item">
                            <h3><?php echo htmlspecialchars(strtoupper($modalidade)); ?></h3>
                            <h4><?php echo htmlspecialchars($graduacao['faixa_atual_nome']); ?></h4>
                            <?php if (!empty($graduacao['conteudo_atual'])): ?>
                                <ul>
                                    <?php foreach ($graduacao['conteudo_atual'] as $conteudo): ?>
                                        <li><?php echo htmlspecialchars($conteudo); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <?php if ($graduacao['proxima_faixa_nome']): ?>
                            <div class="graduacao-item">
                                <h4>Próxima faixa: <?php echo htmlspecialchars($graduacao['proxima_faixa_nome']); ?></h4>
                                <?php if (!empty($graduacao['conteudo_proximo'])): ?>
                                    <ul>
                                        <?php foreach ($graduacao['conteudo_proximo'] as $conteudo): ?>
                                            <li><?php echo htmlspecialchars($conteudo); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section class="section mt-3" id="apostila">
                <h2 class="text-center my-4">APOSTILA</h2>
                <div class="d-flex justify-content-center">
                    <a href="pdf/apostila.pdf" class="btn btn_verde w-100" download>
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