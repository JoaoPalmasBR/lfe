<?php
// Arquivo: pages/campeonatos.php
// Objetivo: Listar os campeonatos públicos disponíveis na API.

// 1. Carrega o helper de funções
require_once ROOT_PATH . '/includes/functions.php';

// 2. Chama a API para buscar os campeonatos
// Usamos a rota pública que criamos anteriormente.
$apiResult = callAPI('GET', '/public/campeonatos.php');

// Verifica se a API retornou sucesso e extrai os dados
$listaCampeonatos = [];
$erroApi = null;

if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
    $listaCampeonatos = $apiResult['data'];
} else {
    // Se houve erro, captura a mensagem
    $erroApi = isset($apiResult['message']) ? $apiResult['message'] : 'Erro desconhecido ao carregar campeonatos.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Campeonatos Disponíveis | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .card-camp {
            transition: transform 0.2s;
        }

        .card-camp:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .badge-estado {
            font-size: 0.8rem;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">LFE</a>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="fw-bold">Campeonatos Disponíveis</h1>
                <p class="lead text-muted">Confira os torneios com inscrições abertas em todo o Brasil.</p>
            </div>
        </div>

        <?php if ($erroApi): ?>
            <div class="alert alert-danger text-center" role="alert">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Ops!</h4>
                <p>Não foi possível carregar a lista de campeonatos no momento.</p>
                <hr>
                <p class="mb-0 small"><?php echo htmlspecialchars($erroApi); ?></p>
            </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

            <?php if (empty($listaCampeonatos) && !$erroApi): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-1"></i>
                    <p class="fs-4 mt-3 text-muted">Nenhum campeonato disponível no momento.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($listaCampeonatos as $camp): ?>
                <?php
                // Define cores/ícones baseados no status
                $statusBadge = 'bg-success';
                $statusTexto = 'Inscrições Abertas';
                if ($camp['status'] === 'checkin_aberto') {
                    $statusBadge = 'bg-warning text-dark';
                    $statusTexto = 'Check-in Aberto';
                } elseif ($camp['status'] === 'em_andamento') {
                    $statusBadge = 'bg-danger';
                    $statusTexto = 'Em Andamento';
                } elseif ($camp['vagas_esgotadas']) {
                    $statusBadge = 'bg-secondary';
                    $statusTexto = 'Vagas Esgotadas (Lista de Espera)';
                }
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0 card-camp">
                        <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                            <span class="badge <?php echo $statusBadge; ?>"><?php echo $statusTexto; ?></span>
                            <span class="badge bg-primary badge-estado" title="<?php echo $camp['nome_estado']; ?>">
                                <i class="bi bi-geo-alt-fill"></i> <?php echo $camp['sigla_estado']; ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary mb-3"><?php echo htmlspecialchars($camp['nome']); ?></h5>

                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-calendar-event me-2"></i> Início:
                                    <strong><?php echo formatarDataHora($camp['data_inicio_prevista']); ?></strong>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-ticket-perforated me-2"></i> Inscrição:
                                    <strong><?php if ($camp['valor_inscricao'] > 0): echo formatarMoeda($camp['valor_inscricao']);
                                            else: echo '<span class="text-success">Gratuito</span>';
                                            endif; ?></strong>
                                </li>
                                <li>
                                    <i class="bi bi-people-fill me-2"></i> Vagas:
                                    <strong><?php echo $camp['total_inscritos']; ?> / <?php echo $camp['limite_participantes']; ?></strong>
                                </li>
                            </ul>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <div class="d-grid">
                                <a href="<?php echo BASE_URL; ?>/cadastro" class="btn btn-outline-primary fw-bold">
                                    Inscrever-se <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>