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

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
} else if ($_SESSION['tipo'] != 'P') {
    echo "<script>alert('Essa página exige acesso com um usuário de professor.'); window.location.href = 'area_professor.php';</script>";
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
    <title>Shaolin Piracicaba | Área do Professor</title>
    
	<style>
	/* RESPONSIVIDADE DA TABELA DE HORÁRIOS */
	@media screen and (max-width: 768px) {
    	/* Oculta a tabela original no mobile */
    	.table-striped {
        	display: none !important;
    	}
    
    	/* Cria versão mobile com cards */
    	.horarios-mobile {
        	display: block !important;
    	}
    
    	.horario-card {
        	background-color: #FDFDFD;
        	border-radius: 8px;
        	padding: 15px;
        	margin-bottom: 15px;
        	box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    	}
    
    	.horario-card h5 {
        	color: #161616;
        	font-weight: 600;
        	margin-bottom: 10px;
        	padding-bottom: 10px;
        	border-bottom: 2px solid #BBCC87;
    	}
    
    	.dia-aula {
        	display: flex;
        	justify-content: space-between;
        	padding: 8px 0;
        	border-bottom: 1px solid #F0F4F9;
    	}
    
    	.dia-aula:last-child {
        	border-bottom: none;
    	}
    
    	.dia-aula .dia {
        	font-weight: 500;
    	}
    
    	.dia-aula .modalidade {
        	color: #161616;
       	 	text-align: right;
    	}
    
    	.dia-aula .vazio {
        	color: #999;
       	 	font-style: italic;
    	}
    
    	/* Ajusta o iframe do calendário no mobile */
    	iframe {
        	height: 400px !important;
    	}
	}

	/* Esconde a versão mobile no desktop */
	@media screen and (min-width: 769px) {
    	.horarios-mobile {
        	display: none !important;
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
                                    <a class="nav-link active" aria-current="page" href="area_professor.php">Área do Professor</a>
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
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <h2 class="text-uppercase mt-4 mb-3 text-center"><b>Área do Professor</b></h2>
                <div id="botoes" class="d-flex justify-content-center flex-wrap w-100">
                    <a class="btn text-uppercase btn_verde" href="cadastro_professores.php">Professores</a>
                    <a class="btn text-uppercase btn_verde" href="cadastro_alunos.php">Alunos</a>
                    <a class="btn text-uppercase btn_verde" href="cadastro_modalidades.php">Modalidades</a>
                    <a class="btn text-uppercase btn_verde" href="alunos_por_modalidade.php">Alunos por modalidade</a>
                </div>
                <h4 class="text-uppercase mt-5 mb-3 text-center">Horários</h4>
                <table class="table table-striped table-bordered text-center">
                    <thead>
                        <tr>
                            <th scope="col">Horário</th>
                            <th scope="col">Segunda-feira</th>
                            <th scope="col">Terça-feira</th>
                            <th scope="col">Quarta-feira</th>
                            <th scope="col">Quinta-feira</th>
                            <th scope="col">Sexta-feira</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">07:00 - 08:00</th>
                            <td>Tai Chi Chuan</td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td>Tai Chi Chuan</td>
                        </tr>
                        <tr>
                            <th scope="row">08:00 - 09:00</th>
                            <td>Shaolin do Norte</td>
                            <td>Treinamento Funcional</td>
                            <td> </td>
                            <td>Treinamento Funcional</td>
                            <td>Shaolin do Norte</td>
                        </tr>                        
                        <tr>
                            <th scope="row">17:00 - 18:00</th>
                            <td>Shaolin do Norte</td>
                            <td>Shaolin do Norte</td>
                            <td>Shaolin do Norte</td>
                            <td>Shaolin do Norte</td>
                            <td>Shaolin do Norte</td>
                        </tr>
                        <tr>
                            <th scope="row">18:00 - 19:00</th>
                            <td>Sanda Boxe Chinês</td>
                            <td>Shaolin Kids</td>
                            <td>Sanda Boxe Chinês</td>
                            <td>Shaolin Kids</td>
                            <td>Sanda Boxe Chinês</td>
                        </tr>
                        <tr>
                            <th scope="row">19:00 - 20:00</th>
                            <td>Shaolin do Norte</td>
                            <td>Sanda Boxe Chinês</td>
                            <td>Shaolin do Norte</td>
                            <td>Sanda Boxe Chinês</td>
                            <td> </td>
                        </tr>
                    </tbody>
                </table>
                <!-- Versão Mobile dos Horários -->
                <div class="horarios-mobile">
                    <div class="horario-card">
                        <h5>07:00 - 08:00</h5>
                        <div class="dia-aula">
                            <span class="dia">Segunda</span>
                            <span class="modalidade">Tai Chi Chuan</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Terça</span>
                            <span class="modalidade vazio">-</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quarta</span>
                            <span class="modalidade vazio">-</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quinta</span>
                            <span class="modalidade vazio">-</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Sexta</span>
                            <span class="modalidade">Tai Chi Chuan</span>
                        </div>
                    </div>

                    <div class="horario-card">
                        <h5>08:00 - 09:00</h5>
                        <div class="dia-aula">
                            <span class="dia">Segunda</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Terça</span>
                            <span class="modalidade">Treinamento Funcional</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quarta</span>
                            <span class="modalidade vazio">-</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quinta</span>
                            <span class="modalidade">Treinamento Funcional</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Sexta</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                    </div>

                    <div class="horario-card">
                        <h5>17:00 - 18:00</h5>
                        <div class="dia-aula">
                            <span class="dia">Segunda</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Terça</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quarta</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quinta</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Sexta</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                    </div>

                    <div class="horario-card">
                        <h5>18:00 - 19:00</h5>
                        <div class="dia-aula">
                            <span class="dia">Segunda</span>
                            <span class="modalidade">Sanda Boxe Chinês</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Terça</span>
                            <span class="modalidade">Shaolin Kids</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quarta</span>
                            <span class="modalidade">Sanda Boxe Chinês</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quinta</span>
                            <span class="modalidade">Shaolin Kids</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Sexta</span>
                            <span class="modalidade">Sanda Boxe Chinês</span>
                        </div>
                    </div>

                    <div class="horario-card">
                        <h5>19:00 - 20:00</h5>
                        <div class="dia-aula">
                            <span class="dia">Segunda</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Terça</span>
                            <span class="modalidade">Sanda Boxe Chinês</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quarta</span>
                            <span class="modalidade">Shaolin do Norte</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Quinta</span>
                            <span class="modalidade">Sanda Boxe Chinês</span>
                        </div>
                        <div class="dia-aula">
                            <span class="dia">Sexta</span>
                            <span class="modalidade vazio">-</span>
                        </div>
                    </div>
                </div>
                
                <h4 class="text-uppercase mt-5 mb-3 text-center">Calendário</h4>
                <iframe width="100%" class="mb-3 rounded-3" src="https://calendar.google.com/calendar/embed?			height=500&wkst=1&ctz=America%2FSao_Paulo&showPrint=0&showTitle=0&showCalendars=0&showTabs=0&hl=pt_BR&src=OGMxNzk4NDhjYzNlN2YzMWE1ZjFhYzQxY2ZkOWM2ZjExOTA3ZTk4NDUxZTNlODM5NzUwZjY4YjM4MWNhOWYyZEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=ZW4uYnJhemlsaWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&color=%23ad1457&color=%230b8043" 				style="border-width:0" width="900" height="500" frameborder="0" scrolling="no"></iframe>
            </div>
        </div>
    </main>
    <footer class="d-flex justify-content-between align-items-center p-4">
        <span>&copy; 2025 Shaolin Kung Fu Piraciaba - Todos os direitos reservados.</span>
        <div class="redes-sociais">
            <i class="fa-brands fa-whatsapp fa-xl m-1" style="color: #161616;"></i>
            <i class="fa-brands fa-facebook fa-xl m-1" style="color: #161616;"></i>
            <i class="fa-brands fa-instagram fa-xl m-1" style="color: #161616;"></i>
        </div>
    </footer>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>