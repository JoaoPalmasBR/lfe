<?php
// Arquivo: pages/home_estado.php
// Esta é a "landing page" específica de um estado (ex: lfe.joao.palmas.br/to)
// A variável global $estadoSlugAtual (definida no index.php) está disponível aqui.
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Página Estadual</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="p-5 mb-4 bg-white rounded-3 shadow-sm text-center">
            <h1 class="display-5 fw-bold text-primary">
                Você está na área do estado: <?php echo strtoupper($estadoSlugAtual); ?>
            </h1>
            <p class="fs-4">
                Esta é a página inicial exclusiva para a federação deste estado.
                O roteamento funcionou perfeitamente!
            </p>
            <a href="<?php echo BASE_URL; ?><?php echo $estadoSlugAtual; ?>/campeonatos" class="btn btn-primary btn-lg">
                Ver Campeonatos de <?php echo strtoupper($estadoSlugAtual); ?>
            </a>
            <br><br>
            <a href="<?php echo BASE_URL; ?>" class="text-muted">Voltar para a Home Global</a>
        </div>
    </div>
</body>

</html>