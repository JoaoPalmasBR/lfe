<?php
// Arquivo: pages/ranking.php
// Objetivo: Página pública do Ranking Global de Jogadores.

require_once ROOT_PATH . '/includes/functions.php';

// Detecta se estamos numa rota de estado (ex: /to/ranking)
$estadoFiltro = $estadoSlugAtual ? strtoupper($estadoSlugAtual) : null;

// Constrói a URL da API com o filtro, se houver
$apiUrl = '/public/ranking.php';
if ($estadoFiltro) {
    $apiUrl .= '?estado_sigla=' . $estadoFiltro;
}

// Chama a API
$apiResult = callAPI('GET', $apiUrl);
$rankingData = [];
$erroApi = null;

if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
    $rankingData = $apiResult['data']['ranking'];
} else {
    $erroApi = isset($apiResult['message']) ? $apiResult['message'] : 'Erro ao carregar o ranking.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Ranking Global | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Estilos para destacar o Top 3 */
        .rank-1 .posicao-badge {
            background-color: #FFD700;
            color: #000;
        }

        /* Ouro */
        .rank-2 .posicao-badge {
            background-color: #C0C0C0;
            color: #000;
        }

        /* Prata */
        .rank-3 .posicao-badge {
            background-color: #CD7F32;
            color: #fff;
        }

        /* Bronze */

        .posicao-badge {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            font-weight: bold;
            background-color: #e9ecef;
            color: #6c757d;
        }

        .avatar-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, .02);
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">LFE Ranking</a>
            <div class="navbar-nav ms-auto">
                <a href="<?php echo BASE_URL; ?>/campeonatos" class="nav-link">Campeonatos</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="text-center mb-5">
            <h1 class="fw-bold display-5"><i class="bi bi-trophy-fill text-warning"></i> Ranking Global</h1>
            <?php if ($estadoFiltro): ?>
                <p class="lead text-primary fw-bold">Região: <?php echo $estadoFiltro; ?></p>
                <a href="<?php echo BASE_URL; ?>/ranking" class="btn btn-sm btn-outline-secondary">Ver Ranking Nacional</a>
            <?php else: ?>
                <p class="lead text-muted">Os melhores atletas da nossa plataforma.</p>
            <?php endif; ?>
        </div>

        <?php if ($erroApi): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($erroApi); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3" style="width: 80px;">Posição</th>
                                <th class="py-3">Jogador</th>
                                <th class="py-3 text-center">Estado</th>
                                <th class="pe-4 py-3 text-end fw-bold">Pontos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rankingData) && !$erroApi): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="bi bi-bar-chart fs-1 d-block mb-3 opacity-50"></i>
                                        Nenhum jogador pontuou ainda nesta região.
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($rankingData as $jogador): ?>
                                <?php
                                // Classe CSS especial para o Top 3
                                $topClass = ($jogador['posicao'] <= 3) ? 'rank-' . $jogador['posicao'] : '';
                                ?>
                                <tr class="<?php echo $topClass; ?>">
                                    <td class="ps-4 fw-bold">
                                        <span class="posicao-badge"><?php echo $jogador['posicao']; ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $jogador['avatar_url']; ?>" alt="Avatar" class="rounded-circle avatar-img shadow-sm me-3">
                                            <span class="fw-semibold fs-5"><?php echo htmlspecialchars($jogador['nome_exibicao']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border font-monospace"><?php echo $jogador['estado_sigla']; ?></span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="fw-bold fs-4 text-primary"><?php echo number_format($jogador['pontuacao_total'], 0, ',', '.'); ?></span>
                                        <small class="text-muted ms-1">pts</small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>