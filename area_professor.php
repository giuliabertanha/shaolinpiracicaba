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
                                    <a class="nav-link active" aria-current="page"  href="login.php">Área do Aluno/Professor</a>
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
    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <h2 class="text-uppercase mt-4 mb-3 text-center"><b>Área do Professor</b></h2>
                <div id="botoes" class="d-flex justify-content-center flex-wrap w-100">
                    <a class="btn text-uppercase btn_verde" href="cadastro_professores.php">Professores</a>
                    <a class="btn text-uppercase btn_verde" href="cadastro_alunos.php">Cadastro de alunos</a>
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
                            <th scope="row">08:00 - 09:00</th>
                            <td>Modalidade A</td>
                            <td>Modalidade A</td>
                            <td>Modalidade A</td>
                            <td>Modalidade C</td>
                            <td>Modalidade C</td>
                        </tr>
                        <tr>
                            <th scope="row">14:00 - 15:00</th>
                            <td>Modalidade B</td>
                            <td>Modalidade B</td>
                            <td>Modalidade A</td>
                            <td>Modalidade A</td>
                            <td>Modalidade A</td>
                        </tr>
                        <tr>
                            <th scope="row">18:00 - 19:00</th>
                            <td>Modalidade C</td>
                            <td>Modalidade C</td>
                            <td>Modalidade B</td>
                            <td>Modalidade B</td>
                            <td>Modalidade B</td>
                        </tr>
                        <tr>
                            <th scope="row">19:00 - 20:00</th>
                            <td>Modalidade A</td>
                            <td>Modalidade A</td>
                            <td>Modalidade B</td>
                            <td>Modalidade B</td>
                            <td>Modalidade B</td>
                        </tr>
                    </tbody>
                </table>
                <h4 class="text-uppercase mt-5 mb-3 text-center">Calendário</h4>
                <iframe width="100%" class="mb-3 rounded-3" src="https://calendar.google.com/calendar/embed?height=500&wkst=1&ctz=America%2FSao_Paulo&showPrint=0&showTitle=0&showCalendars=0&showTabs=0&hl=pt_BR&src=OGMxNzk4NDhjYzNlN2YzMWE1ZjFhYzQxY2ZkOWM2ZjExOTA3ZTk4NDUxZTNlODM5NzUwZjY4YjM4MWNhOWYyZEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=ZW4uYnJhemlsaWFuI2hvbGlkYXlAZ3JvdXAudi5jYWxlbmRhci5nb29nbGUuY29t&color=%23ad1457&color=%230b8043" style="border-width:0" width="900" height="500" frameborder="0" scrolling="no"></iframe>
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