<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "shaolin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql_a = "SELECT usuarios.nome AS 'Aluno', modA.faixa AS 'Faixa' FROM modA INNER JOIN usuarios ON usuarios.id = modA.id_aluno WHERE usuarios.tipo='A';"; 
$sql_b = "SELECT usuarios.nome AS 'Aluno', modB.faixa AS 'Faixa' FROM modB INNER JOIN usuarios ON usuarios.id = modB.id_aluno WHERE usuarios.tipo='A';"; 
$sql_c = "SELECT usuarios.nome AS 'Aluno', modC.faixa AS 'Faixa' FROM modC INNER JOIN usuarios ON usuarios.id = modC.id_aluno WHERE usuarios.tipo='A';"; 
    
$result_a = $conn->query($sql_a);
$result_b = $conn->query($sql_b);
$result_c = $conn->query($sql_c);

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
    <title>Shaolin Piracicaba | Alunos por modalidade</title>
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
                                    <a class="nav-link active" aria-current="page" href="login.php">Área do Aluno/Professor</a>
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
		<h2 class="text-uppercase mt-4 mb-3 text-center"><b>Alunos por modalidade</b></h2>  
        <div class="d-flex w-75">
        <table class="table table-striped m-4 rounded">
          <thead>
            <tr>
              <th scope="col" colspan='2'>Modalidade A</th>
            </tr>
          </thead>
          <tbody>
              <?php
                if ($result_a->num_rows > 0) {
                    while($row = $result_a->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='#'>" . htmlspecialchars($row["Aluno"]) . "</a></td>";
                        echo "<td class='w-25'>" . htmlspecialchars($row["Faixa"]) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>0 resultados</td></tr>";
                }
                ?>
          </tbody>
        </table>
        <table class="table table-striped m-4">
          <thead>
            <tr>
              <th scope="col" colspan='2'>Modalidade B</th>
            </tr>
          </thead>
          <tbody>
              <?php
                if ($result_b->num_rows > 0) {
                    while($row = $result_b->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='#'>" . htmlspecialchars($row["Aluno"]) . "</a></td>";
                        echo "<td class='w-25'>" . htmlspecialchars($row["Faixa"]) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>0 resultados</td></tr>";
                }
                ?>
          </tbody>
        </table>
        <table class="table table-striped m-4">
          <thead>
            <tr>
              <th scope="col" colspan='2'>Modalidade C</th>
            </tr>
          </thead>
          <tbody>
              <?php
                if ($result_c->num_rows > 0) {
                    while($row = $result_c->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><a href='#'>" . htmlspecialchars($row["Aluno"]) . "</a></td>";
                        echo "<td class='w-25'>" . htmlspecialchars($row["Faixa"]) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>0 resultados</td></tr>";
                }
                ?>
          </tbody>
        </table>
        </div>
        <a class="btn text-uppercase w-75 me-0 voltar" href="area_professor.php">Voltar</a>
    </main>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>