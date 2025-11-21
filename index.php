<?php
// --- Configurações PHP ---
$pageTitle = "Em Breve";
$leagueName = "Liga Tocantinense de Futebol Eletrônico";
$leagueAcronym = "LTFE";
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
        /* Garante que o body ocupe 100% da altura */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
            /* Um fundo levemente cinza */
        }

        /* * 'flex: 1' faz o <main> crescer e ocupar o espaço disponível.
         * As outras duas linhas centralizam o conteúdo verticalmente.
        */
        main {
            flex: 1;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold">
                <?php echo $leagueAcronym; ?>
            </a>
        </div>
    </nav>

    <main>
        <div class="container text-center px-4 py-5">

            <h1 class="display-4 fw-bold mb-3">
                <?php echo $leagueName; ?>
            </h1>

            <p class="lead text-muted mb-4">
                Estamos preparando o campo. A maior competição de e-sports do Tocantins começa em breve.
            </p>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a class="btn btn-primary btn-lg px-4 gap-3">
                    Seja Notificado
                </a>
            </div>

        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">
                &copy; <?php echo $currentYear; ?> <?php echo $leagueAcronym; ?>.
            </span>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>