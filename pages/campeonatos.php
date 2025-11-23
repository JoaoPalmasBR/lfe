<?php
// Arquivo: pages/campeonatos.php
// Objetivo: Vitrine de campeonatos. Se logado, permite inscrição direta.

require_once ROOT_PATH . '/includes/functions.php';

// =====================================================================
// 1. VERIFICAÇÃO DE LOGIN (Sem bloqueio)
// =====================================================================
// Precisamos saber se o usuário está logado para mudar o comportamento do botão.
// Não usamos o auth_guard.php aqui porque esta página é pública.

$usuarioLogadoVitrine = null; // Variável que guardará o usuário se ele estiver logado
$tokenVitrine = null;

if (isset($_COOKIE['lfe_token']) && !empty($_COOKIE['lfe_token'])) {
    $tokenVitrine = $_COOKIE['lfe_token'];
    // Tenta buscar os dados do usuário na API
    $apiAuth = callAPI('GET', '/user/me.php', null, $tokenVitrine);
    if (isset($apiAuth['status']) && $apiAuth['status'] === 'success') {
        $usuarioLogadoVitrine = $apiAuth['data'];
        // Verifica se é um JOGADOR (admins não se inscrevem)
        if ($usuarioLogadoVitrine['funcao'] !== 'jogador') {
            $usuarioLogadoVitrine = null; // Reseta se não for jogador
        }
    }
}

// =====================================================================
// 2. PROCESSAMENTO DE INSCRIÇÃO (Se logado e clicou no botão)
// =====================================================================
$erroInscricao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_inscrever']) && $usuarioLogadoVitrine) {
    $campIdAlvo = (int)$_POST['campeonato_id'];

    // Chama a API de inscrição protegida
    $payload = ['campeonato_id' => $campIdAlvo];
    $apiInscricao = callAPI('POST', '/jogador/inscrever.php', $payload, $tokenVitrine);

    if (isset($apiInscricao['status']) && $apiInscricao['status'] === 'success') {
        // SUCESSO! Redireciona para o painel com mensagem.
        header("Location: " . BASE_URL . "/painel?msg=inscricao_sucesso");
        exit;
    } else {
        // FALHA (Ex: já inscrito, sem vaga)
        $erroInscricao = isset($apiInscricao['message']) ? $apiInscricao['message'] : "Erro ao realizar inscrição.";
    }
}

// =====================================================================
// 3. CARREGAR CAMPEONATOS DA API
// =====================================================================
$apiResult = callAPI('GET', '/public/campeonatos.php');
$listaCampeonatos = [];
$erroApi = null;

if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
    $listaCampeonatos = $apiResult['data'];
} else {
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

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">LFE</a>
            <?php if ($usuarioLogadoVitrine): ?>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link active fw-bold" href="<?php echo BASE_URL; ?>/painel">
                        <i class="bi bi-person-circle me-1"></i> Meu Painel (<?php echo htmlspecialchars($usuarioLogadoVitrine['nome_completo']); ?>)
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="fw-bold">Campeonatos Disponíveis</h1>
                <?php if ($estadoSlugAtual): ?>
                    <p class="lead text-primary fw-bold">Região: <?php echo strtoupper($estadoSlugAtual); ?></p>
                <?php else: ?>
                    <p class="lead text-muted">Confira os torneios com inscrições abertas em todo o Brasil.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($erroApi): ?>
            <div class="alert alert-danger text-center"><i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($erroApi); ?></div>
        <?php endif; ?>

        <?php if ($erroInscricao): ?>
            <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i> <strong>Não foi possível se inscrever:</strong> <?php echo htmlspecialchars($erroInscricao); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

            <?php if (empty($listaCampeonatos) && !$erroApi): ?>
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-calendar-x display-1"></i>
                    <p class="fs-4 mt-3">Nenhum campeonato disponível no momento.</p>
                </div>
            <?php endif; ?>

            <?php foreach ($listaCampeonatos as $camp): ?>
                <?php
                // Filtro de Estado (se estivermos em /to, só mostra de TO)
                if ($estadoSlugAtual && strtolower($camp['sigla_estado']) !== $estadoSlugAtual) {
                    continue; // Pula este campeonato
                }

                $statusBadge = 'bg-success';
                $statusTexto = 'Inscrições Abertas';
                $podeInscrever = ($camp['status'] === 'inscricoes_abertas' && !$camp['vagas_esgotadas']);

                if ($camp['status'] === 'checkin_aberto') {
                    $statusBadge = 'bg-warning text-dark';
                    $statusTexto = 'Check-in Aberto';
                } elseif ($camp['status'] === 'em_andamento') {
                    $statusBadge = 'bg-danger';
                    $statusTexto = 'Em Andamento';
                    $podeInscrever = false;
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
                            <h5 class="card-title fw-bold text-primary mb-3 text-truncate" title="<?php echo htmlspecialchars($camp['nome']); ?>"><?php echo htmlspecialchars($camp['nome']); ?></h5>
                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-2"><i class="bi bi-calendar-event me-2"></i> Início: <strong><?php echo formatarDataHora($camp['data_inicio_prevista']); ?></strong></li>
                                <li class="mb-2"><i class="bi bi-ticket-perforated me-2"></i> Inscrição: <strong><?php if ($camp['valor_inscricao'] > 0): echo formatarMoeda($camp['valor_inscricao']);
                                                                                                                    else: echo '<span class="text-success">Gratuito</span>';
                                                                                                                    endif; ?></strong></li>
                                <li><i class="bi bi-people-fill me-2"></i> Vagas: <strong><?php echo $camp['total_inscritos']; ?> / <?php echo $camp['limite_participantes']; ?></strong></li>
                            </ul>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <div class="d-grid">
                                <?php if ($podeInscrever): ?>
                                    <?php if ($usuarioLogadoVitrine): ?>
                                        <form method="POST" onsubmit="return confirm('Confirmar inscrição em: <?php echo htmlspecialchars($camp['nome']); ?>?');">
                                            <input type="hidden" name="campeonato_id" value="<?php echo $camp['id']; ?>">
                                            <button type="submit" name="acao_inscrever" class="btn btn-primary fw-bold w-100">
                                                Confirmar Inscrição <i class="bi bi-check-circle-fill"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>/cadastro" class="btn btn-outline-primary fw-bold">
                                            Inscrever-se <i class="bi bi-arrow-right-short"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Inscrições Encerradas</button>
                                <?php endif; ?>
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