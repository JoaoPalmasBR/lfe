<?php
    // --- Configurações Simples em PHP ---

    // Título da Página
    $pageTitle = "Bem-vindo";

    // Nome da Liga
    $leagueName = "Liga Tocantinense de Futebol Eletrônico";
    $leagueAcronym = "LTFE";

    // Ano atual para o rodapé
    $currentYear = date("Y");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo $pageTitle; ?> | <?php echo $leagueAcronym; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <?php echo $leagueAcronym; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Regulamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Times</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-lg-2 px-3" href="#">Inscreva-se</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container col-xxl-8 px-4 py-5">
            <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
                <div class="col-10 col-sm-8 col-lg-6">
                    
                    <img src="https://via.placeholder.com/700x450/6c757d/ffffff?text=Logo+LTFE" class="d-block mx-lg-auto img-fluid rounded" alt="Logo da LTFE" width="700" height="500" loading="lazy">
                </div>
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold lh-1 mb-3">
                        <?php echo $leagueName; ?>
                    </h1>
                    <p class="lead">
                        O campeonato oficial de futebol eletrônico do Tocantins. Prepare seu time, mostre sua habilidade e entre para a história do e-sport tocantinense.
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a href="#" class="btn btn-primary btn-lg px-4 me-md-2">Ver Competições</a>
                        <a href="#" class="btn btn-outline-secondary btn-lg px-4">Saiba Mais</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container px-4 py-5 text-center">
            <h2 class="pb-2 border-bottom">O que vem por aí</h2>
            <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
                <div class="col">
                    <h3>Temporada 1</h3>
                    <p>A primeira temporada da liga está sendo preparada. Fique de olho para a abertura das inscrições e o regulamento oficial.</p>
                </div>
                <div class="col">
                    <h3>Plataforma</h3>
                    <p>Estamos construindo o melhor site para gerenciamento de jogos, tabelas, estatísticas e perfis de jogadores.</p>
                </div>
                <div class="col">
                    <h3>Comunidade</h3>
                    <p>Participe do nosso servidor no Discord (em breve) para encontrar jogadores, marcar amistosos e ficar por dentro das novidades.</p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-dark text-white-50">
        <div class="container text-center">
            <span class="text-white">
                &copy; <?php echo $currentYear; ?> <?php echo $leagueAcronym; ?>. Todos os direitos reservados.
            </span>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>