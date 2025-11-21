<?php
// --- Configurações PHP ---
// Aqui definimos variáveis básicas para usar no HTML abaixo
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
        /* Garante que o corpo da página ocupe pelo menos 100% da altura da tela */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #ffffff;
            /* Fundo branco para combinar com o logo */
        }

        /* * 'flex: 1' faz a tag <main> crescer para ocupar todo o espaço disponível entre o topo e o rodapé.
         * 'display: flex' e 'align-items: center' centralizam o conteúdo verticalmente dentro do main.
        */
        main {
            flex: 1;
            display: flex;
            align-items: center;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <?php echo $leagueAcronym; ?>
            </a>
        </div>
    </nav>

    <main>
        <div class="container text-center px-4 py-5">

            <img src="logo-ltfe.png" alt="Logo da LTFE" width="220" class="img-fluid mb-4">

            <h1 class="display-5 fw-bold mb-3 text-dark">
                <?php echo $leagueName; ?>
            </h1>

            <p class="lead text-muted mb-5">
                Estamos preparando o campo. A maior competição oficial de e-sports do Tocantins começa em breve.
            </p>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="#" class="btn btn-primary btn-lg px-5 gap-3 fw-bold rounded-pill">
                    Seja Notificado do Lançamento
                </a>
            </div>

        </div>
    </main>

    <footer class="footer mt-auto py-3 bg-light text-center">
        <div class="container">
            <span class="text-muted small">
                &copy; <?php echo $currentYear; ?> <?php echo $leagueName; ?>.
            </span>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>