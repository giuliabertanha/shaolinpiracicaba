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
    <link rel="stylesheet" href="css/styles1.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="img/icon.png" type="image/x-icon">
    <title>Shaolin Piracicaba | Premiações</title>
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
                                    <a class="nav-link active" aria-current="page" href="premiacoes.php">Premiações</a>
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
    
    <main class="container_premiacao py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                
                <h2 class="text-uppercase mt-4 mb-5 text-center"><b>PREMIAÇÕES</b></h2>

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <img src="https://ik.imagekit.io/shaolin/img/premios_geral.jpeg?updatedAt=1763122098084" alt="Títulos e Conquistas" class="img-premiacao">
                        </div>
                        <div class="col-md-7">
                            <p>
                                Ao longo de sua trajetória nas artes marciais, Paulo Medeiros construiu uma carreira marcada 
                                por dedicação, disciplina e excelentes resultados em competições nacionais e internacionais. 
                                Sua busca constante pela excelência técnica e pelo aperfeiçoamento pessoal o levou a conquistar 
                                diversos prêmios e reconhecimentos dentro do Kung Fu.
                            </p>
                            <p>
                                Entre suas principais conquistas, destacam-se 8x títulos nacionais, 2x títulos Sul-americano 
                                e 1x título Panamericano. Além de diversos outros campeonatos estaduais e regionais. Sua 
                                atuação como atleta e técnico tem inspirado alunos e praticantes, demonstrando que o verdadeiro 
                                espírito do Kung Fu vai muito além das vitórias — ele está na superação diária e na busca constante pelo aprimoramento.
                            </p>
                            <p>
                                Esses resultados refletem não apenas o talento, mas também o compromisso de professor 
                                Paulo Medeiros com a preservação e o fortalecimento da arte marcial chinesa no Brasil.
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="hr-separador">

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="text-uppercase fw-bold mb-3">MUNDIAL</h4>
                            <p>
                                Este evento é organizado pela Federação Internacional de Wushu (IWUF) e, diferentemente do World 
                                Wushu Championships (que foca no Wushu moderno/esportivo), o WKFC é dedicado exclusivamente aos 
                                estilos tradicionais de Kung Fu.
                                Ele acontece a cada dois anos e reúne praticantes de todas as idades para competir e 
                                promover uma troca cultural. A China, como berço da arte marcial, frequentemente sedia o 
                                evento em locais icônicos, como Emeishan (sede da 10ª edição).
                            </p>
                        </div>
                        <div class="col-md-6">
                            <img src="https://ik.imagekit.io/shaolin/img/mundial.jpeg?updatedAt=1763121283035" alt="Campeonato Mundial" class="img-premiacao">
                        </div>
                    </div>
                </div>

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <img src="https://ik.imagekit.io/shaolin/img/sulameriacano.jpeg?updatedAt=1763121282951" alt="Pódio do Sul-Americano" class="img-premiacao">
                        </div>
                        <div class="col-md-6">
                            <h4 class="text-uppercase fw-bold mb-3">SUL-AMERICANO</h4>
                            <p>
                                Este é um campeonato continental de alto nível onde os atletas competem representando 
                                seus países. A participação não é aberta; os atletas são selecionados com base em seu 
                                desempenho em eventos nacionais pela confederação oficial de seu país (no Brasil, a CBKW - 
                                Confederação Brasileira de Kungfu Wushu).
                                O evento cobre diversas modalidades, incluindo Taolu (formas tradicionais) e Sanda 
                                (combate), e é sediado por diferentes países-membros na América do Sul, como a Argentina.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="text-uppercase fw-bold mb-3">BRASILEIRO</h4>
                            <p>
                                Este é o principal evento nacional do calendário da CBKW. É a competição onde atletas 
                                de todo o Brasil se encontram para disputar o título de campeão brasileiro em suas respectivas 
                                categorias e modalidades (como Sanda e Taolu).
                                Além de definir os campeões nacionais, o Campeonato Brasileiro é a principal forma de 
                                somar pontos para o Ranking CBKW e serve como uma das etapas cruciais para a seleção de 
                                atletas que irão compor a seleção brasileira em competições internacionais.
                            </p>
                        </div>
                        <div class="col-md-6">
                            <img src="https://ik.imagekit.io/shaolin/img/brasileiro.jpeg?updatedAt=1763121283081" alt="Pódio do Campeonato Brasileiro" class="img-premiacao">
                        </div>
                    </div>
                </div>

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <img src="https://ik.imagekit.io/shaolin/img/melhor_ano.jpeg?updatedAt=1763121283044" alt="Prêmio Melhores do Ano" class="img-premiacao">
                        </div>
                        <div class="col-md-6">
                            <h4 class="text-uppercase fw-bold mb-3">MELHORES DO ANO</h4>
                            <p>
                                Este não é um torneio único, mas sim um prêmio de reconhecimento anual concedido 
                                pela CBKW. Ele homenageia os atletas que mais se destacaram ao longo da temporada de competições.
                                O prêmio é definido pelo Ranking CBKW: o atleta que acumula a maior pontuação 
                                em sua respectiva categoria (Sanda, Taolu Esportivo, Taolu Tradicional, etc.) ao final do ano é declarado o "Melhor do Ano".
                            </p>
                        </div>
                    </div>
                </div>

                <hr class="hr-separador">

                <h2 class="text-uppercase mt-4 mb-5 text-center"><b>CONQUISTAS DOS ALUNOS</b></h2>

                <div class="secao-premiacao mx-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <p>
                                Ao longo dos anos, os alunos da Academia Shaolin Kung Fu têm se destacado em diversas competições
                                e eventos da modalidade, representando o nome da nossa escola e da nossa cidade com muito orgulho e dedicação.
                                Sob a orientação dos professores e mestres dedicados, conquistamos títulos regionais, estaduais e nacionais, 
                                demonstrando não apenas técnica e disciplina, mas também o verdadeiro espírito das artes marciais chinesas.
                            </p>
                            <p>
                                Entre as conquistas mais marcantes, destacam-se:<br>
                                Campeonatos de formas tradicionais (Taolu), Pódios em competições de Sanda (boxe chinês) e formas com armas tradicionais;<br>
                                Participações em festivais culturais e demonstrações em eventos festivos, promovendo a arte do Kung Fu 
                                e a filosofia do equilíbrio entre corpo e mente.
                            </p>
                            <p>
                                Mais do que medalhas, cada vitória representa o resultado de anos de treino, respeito, 
                                superação e amizade — valores que cultivamos diariamente dentro e fora do tatame.
                            </p>
                        </div>
                        <div class="col-md-5">
                            <img src="https://ik.imagekit.io/shaolin/img/alunos_premios.jpeg?updatedAt=1763121283306" alt="Conquistas dos Alunos" class="img-premiacao">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <footer class="d-flex justify-content-between align-items-center p-4">
        <span>© 2025 Shaolin Kung Fu Piraciaba - Todos os direitos reservados.</span>
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