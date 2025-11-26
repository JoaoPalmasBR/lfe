<?php
// Arquivo: /painel/views/gestor_campeonato_detalhes.php
// View: Tela de gerenciamento profundo de UM campeonato.
// Variáveis disponíveis: $currentUserFront, $userTokenFront, $routerParams['id']

$campId = isset($routerParams['id']) ? (int)$routerParams['id'] : 0;

if ($campId === 0) {
    die("Erro: ID do campeonato inválido.");
}

$msgSucesso = null;
$msgErro = null;

// =====================================================================
// 1. PROCESSAR AÇÕES (POST)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AÇÃO 1: MUDAR STATUS
    if (isset($_POST['acao_mudar_status'])) {
        $novoStatus = $_POST['novo_status'];
        $apiResult = callAPI('PUT', "/gestor/campeonato_edicao.php?id=$campId", ['status' => $novoStatus], $userTokenFront);
        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $msgSucesso = "Status atualizado para: " . strtoupper($novoStatus);
        } else {
            $msgErro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao atualizar status.";
        }
    }
    // AÇÃO 2: SIMULAR PAGAMENTO
    if (isset($_POST['acao_simular_pagamento'])) {
        $inscricaoIdAlvo = (int)$_POST['inscricao_id'];
        $apiResult = callAPI('POST', "/gestor/simular_pagamento.php", ['inscricao_id' => $inscricaoIdAlvo], $userTokenFront);
        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $msgSucesso = "Pagamento confirmado para a inscrição #$inscricaoIdAlvo.";
        } else {
            $msgErro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao confirmar pagamento.";
        }
    }
}

// =====================================================================
// 2. BUSCAR DADOS DA API
// =====================================================================
$apiCamp = callAPI('GET', "/gestor/campeonato_detalhes.php?id=$campId", null, $userTokenFront);
if (!isset($apiCamp['status']) || $apiCamp['status'] !== 'success') {
    header("Location: " . BASE_URL . "/painel?msg=erro_acesso_campeonato");
    exit;
}
$camp = $apiCamp['data'];

$apiInscritos = callAPI('GET', "/gestor/inscritos.php?campeonato_id=$campId", null, $userTokenFront);
$listaInscritos = ($apiInscritos['status'] === 'success') ? $apiInscritos['data'] : [];

$badgesStatus = [
    'rascunho' => ['bg-secondary', 'Rascunho (Invisível)'],
    'inscricoes_abertas' => ['bg-success', 'Inscrições Abertas'],
    'checkin_aberto' => ['bg-warning text-dark', 'Check-in Aberto'],
    'em_andamento' => ['bg-danger', 'Em Andamento'],
    'finalizado' => ['bg-dark', 'Finalizado']
];
$campBadge = $badgesStatus[$camp['status']] ?? ['bg-light text-dark', $camp['status']];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Gerenciar: <?php echo htmlspecialchars($camp['nome']); ?> | LFE Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .bg-gestor-primary {
            background-color: #0d6efd;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-gestor-primary mb-4 shadow">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/painel">
                <i class="bi bi-arrow-left-circle me-2"></i> Voltar ao Dashboard
            </a>
            <span class="navbar-text text-white small">Gerenciando Evento #<?php echo $camp['id']; ?></span>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">
        <?php if ($msgSucesso): ?><div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle-fill me-2"></i> <?php echo $msgSucesso; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
        <?php if ($msgErro): ?><div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $msgErro; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-md-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge mb-2 <?php echo $campBadge[0]; ?>"><?php echo $campBadge[1]; ?></span>
                        <h2 class="fw-bold text-primary mb-3"><?php echo htmlspecialchars($camp['nome']); ?></h2>
                        <ul class="list-inline text-muted small mb-0">
                            <li class="list-inline-item me-3"><i class="bi bi-calendar-event"></i> <?php echo formatarDataHora($camp['data_inicio_prevista']); ?></li>
                            <li class="list-inline-item me-3"><i class="bi bi-cash-coin"></i> Inscrição: <?php echo formatarMoeda($camp['valor_inscricao']); ?></li>
                            <li class="list-inline-item"><i class="bi bi-people-fill"></i> Vagas: <?php echo $camp['limite_participantes']; ?></li>
                        </ul>
                    </div>
                    <div class="mt-3 mt-md-0 text-end">
                        <p class="small text-muted fw-bold mb-2">Ações do Evento:</p>
                        <?php if ($camp['status'] === 'rascunho'): ?>
                            <form method="POST" class="d-inline"><input type="hidden" name="novo_status" value="inscricoes_abertas"><button type="submit" name="acao_mudar_status" class="btn btn-success fw-bold"><i class="bi bi-megaphone-fill me-1"></i> PUBLICAR TORNEIO</button></form>
                        <?php elseif ($camp['status'] === 'inscricoes_abertas'): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja fechar as inscrições e abrir o check-in?');"><input type="hidden" name="novo_status" value="checkin_aberto"><button type="submit" name="acao_mudar_status" class="btn btn-warning text-dark fw-bold"><i class="bi bi-door-closed-fill me-1"></i> FECHAR INSCRIÇÕES E ABRIR CHECK-IN</button></form>
                        <?php else: ?><button class="btn btn-secondary" disabled>Status não alterável por aqui</button><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span class="text-primary"><i class="bi bi-person-lines-fill me-2"></i> Jogadores Inscritos</span>
                <div><span class="badge bg-success me-1"><?php echo $camp['total_confirmados']; ?> pagos</span><span class="badge bg-secondary"><?php echo $camp['total_inscritos_geral']; ?> total</span></div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-muted text-uppercase">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Status</th>
                                <th>Jogador (Nick)</th>
                                <th>Data Inscrição</th>
                                <th>Ações Financeiras</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php if (empty($listaInscritos)): ?><tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Nenhum jogador inscrito ainda.</td>
                                </tr><?php else: ?>
                                <?php foreach ($listaInscritos as $inscrito):
                                            $pBadge = 'bg-secondary';
                                            $pTexto = $inscrito['status'];
                                            if ($inscrito['status'] === 'aguardando_pagamento') {
                                                $pBadge = 'bg-warning text-dark';
                                                $pTexto = 'Aguardando Pagto';
                                            } elseif ($inscrito['status'] === 'confirmado') {
                                                $pBadge = 'bg-success';
                                                $pTexto = 'Confirmado';
                                            } elseif ($inscrito['status'] === 'lista_espera') {
                                                $pBadge = 'bg-info text-dark';
                                                $pTexto = 'Lista de Espera';
                                            } elseif ($inscrito['status'] === 'checkin_realizado') {
                                                $pBadge = 'bg-primary';
                                                $pTexto = 'Check-in Feito';
                                            }
                                ?>
                                    <tr>
                                        <td class="ps-4 small text-muted">#<?php echo $inscrito['inscricao_id']; ?></td>
                                        <td><span class="badge <?php echo $pBadge; ?> small"><?php echo $pTexto; ?></span></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($inscrito['nome_completo']); ?></div>
                                            <div class="small text-muted">@<?php echo htmlspecialchars($inscrito['nickname']); ?></div>
                                        </td>
                                        <td class="small text-muted"><?php echo formatarDataHora($inscrito['data_inscricao']); ?></td>
                                        <td>
                                            <?php if ($inscrito['status'] === 'aguardando_pagamento'): ?>
                                                <form method="POST" onsubmit="return confirm('Confirmar pagamento de <?php echo htmlspecialchars($inscrito['nickname']); ?>?');"><input type="hidden" name="inscricao_id" value="<?php echo $inscrito['inscricao_id']; ?>"><button type="submit" name="acao_simular_pagamento" class="btn btn-sm btn-outline-success fw-bold"><i class="bi bi-cash-coin"></i> Confirmar Pagto</button></form>
                                            <?php elseif (in_array($inscrito['status'], ['confirmado', 'checkin_realizado'])): ?><span class="text-success small fw-bold"><i class="bi bi-check-all"></i> Pago</span><?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4" id="secao-partidas">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span class="text-primary"><i class="bi bi-diagram-3-fill me-2"></i> Partidas & Chaveamento</span>
                <?php
                $numAptos = 0;
                foreach ($listaInscritos as $ins) {
                    if (in_array($ins['status'], ['confirmado', 'checkin_realizado'])) $numAptos++;
                }
                $podeGerarChaves = (in_array($camp['status'], ['inscricoes_abertas', 'checkin_aberto']) && $numAptos >= 2);
                ?>
                <?php if ($podeGerarChaves): ?>
                    <button id="btnGerarChaves" class="btn btn-primary btn-sm fw-bold shadow-sm" onclick="gerarChavesTorneio()"><i class="bi bi-shuffle me-2"></i> Gerar 1ª Rodada <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span></button>
                <?php endif; ?>
            </div>
            <div class="card-body bg-light p-4">
                <div id="lista-partidas-loader" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>
                    <p class="mt-2 text-muted">Buscando confrontos...</p>
                </div>
                <div id="lista-partidas-vazia" class="alert alert-info text-center d-none"><i class="bi bi-info-circle fs-4 d-block mb-2"></i> Nenhuma partida gerada ainda.<?php if ($podeGerarChaves): ?><br>Use o botão acima para iniciar.<?php endif; ?></div>
                <div id="lista-partidas-container" class="row g-3 d-none"></div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="modalEditarPartida" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light py-2">
                    <h6 class="modal-title fw-bold">Lançar Resultado</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <input type="hidden" id="editPartidaId">
                    <p class="text-muted small mb-3 text-uppercase ls-1" id="editPartidaTitulo"></p>
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <div style="flex: 1; max-width: 45%;"><strong class="d-block text-truncate small mb-1" id="editJ1Nome"></strong><input type="number" id="editJ1Placar" class="form-control form-control-lg text-center fw-bold bg-light border-0" min="0" placeholder="0"></div>
                        <div class="h5 mx-2 text-muted opacity-50">X</div>
                        <div style="flex: 1; max-width: 45%;"><strong class="d-block text-truncate small mb-1" id="editJ2Nome"></strong><input type="number" id="editJ2Placar" class="form-control form-control-lg text-center fw-bold bg-light border-0" min="0" placeholder="0"></div>
                    </div>
                    <div class="mb-3 text-start bg-light p-3 rounded-3 border">
                        <label class="form-label small fw-bold text-muted text-uppercase ls-1 mb-2 d-block">Quem avançou?</label>
                        <div class="form-check mb-2"><input class="form-check-input" type="radio" name="radioVencedor" id="radioVencedorJ1" value="j1"><label class="form-check-label small fw-bold" for="radioVencedorJ1" id="labelVencedorJ1">Jogador 1</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="radioVencedor" id="radioVencedorJ2" value="j2"><label class="form-check-label small fw-bold" for="radioVencedorJ2" id="labelVencedorJ2">Jogador 2</label></div>
                    </div>
                    <div class="d-grid"><button type="button" id="btnSalvarPlacar" class="btn btn-success fw-bold" onclick="salvarPlacarPartida()"><i class="bi bi-check-circle-fill me-2"></i> FINALIZAR PARTIDA</button></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>',
            CAMPEONATO_ID: <?php echo $campId; ?>
        };
    </script>

    <script src="<?php echo BASE_URL; ?>/painel/js/gestor_partidas.js?v=<?php echo time(); ?>"></script>
</body>

</html>