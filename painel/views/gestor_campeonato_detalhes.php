<?php
// Arquivo: /painel/views/gestor_campeonato_detalhes.php
// View: Tela de gerenciamento profundo de UM campeonato.
// Variáveis disponíveis: $currentUserFront, $userTokenFront, $routerParams['id']

$campId = isset($routerParams['id']) ? (int)$routerParams['id'] : 0;

if ($campId === 0) {
    // Se chegou aqui sem ID, algo deu muito errado no roteador.
    die("Erro: ID do campeonato inválido.");
}

// Mensagens de feedback para o usuário
$msgSucesso = null;
$msgErro = null;

// =====================================================================
// 1. PROCESSAR AÇÕES (POST) - Se o gestor clicou em algum botão
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- AÇÃO 1: MUDAR STATUS DO TORNEIO ---
    if (isset($_POST['acao_mudar_status'])) {
        $novoStatus = $_POST['novo_status'];
        // Chama a API de edição (PUT)
        $apiResult = callAPI('PUT', "/gestor/campeonato_edicao.php?id=$campId", ['status' => $novoStatus], $userTokenFront);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $msgSucesso = "Status do campeonato atualizado para: " . strtoupper($novoStatus);
        } else {
            $msgErro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao atualizar status.";
        }
    }

    // --- AÇÃO 2: SIMULAR PAGAMENTO DE INSCRITO ---
    if (isset($_POST['acao_simular_pagamento'])) {
        $inscricaoIdAlvo = (int)$_POST['inscricao_id'];
        // Chama a API de simulação financeira (POST)
        $apiResult = callAPI('POST', "/gestor/simular_pagamento.php", ['inscricao_id' => $inscricaoIdAlvo], $userTokenFront);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $msgSucesso = "Pagamento confirmado para a inscrição #$inscricaoIdAlvo.";
        } else {
            $msgErro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao confirmar pagamento.";
        }
    }
}

// =====================================================================
// 2. BUSCAR DADOS ATUALIZADOS DA API
// =====================================================================

// Busca detalhes do campeonato
$apiCamp = callAPI('GET', "/gestor/campeonato_detalhes.php?id=$campId", null, $userTokenFront);

if (!isset($apiCamp['status']) || $apiCamp['status'] !== 'success') {
    // Se deu erro (ex: 404 não encontrado ou não pertence ao gestor)
    // Redireciona de volta para o dashboard com erro.
    header("Location: " . BASE_URL . "/painel?msg=erro_acesso_campeonato");
    exit;
}
$camp = $apiCamp['data'];

// Busca lista de inscritos
$apiInscritos = callAPI('GET', "/gestor/inscritos.php?campeonato_id=$campId", null, $userTokenFront);
$listaInscritos = ($apiInscritos['status'] === 'success') ? $apiInscritos['data'] : [];

// Configura badges visualmente
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
            <span class="navbar-text text-white small">
                Gerenciando Evento #<?php echo $camp['id']; ?>
            </span>
        </div>
    </nav>

    <div class="container-fluid px-4 pb-5">

        <?php if ($msgSucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <?php echo $msgSucesso; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($msgErro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $msgErro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>


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
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="novo_status" value="inscricoes_abertas">
                                <button type="submit" name="acao_mudar_status" class="btn btn-success fw-bold">
                                    <i class="bi bi-megaphone-fill me-1"></i> PUBLICAR TORNEIO
                                </button>
                            </form>
                        <?php elseif ($camp['status'] === 'inscricoes_abertas'): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja fechar as inscrições e abrir o check-in? Isso impedirá novas entradas.');">
                                <input type="hidden" name="novo_status" value="checkin_aberto">
                                <button type="submit" name="acao_mudar_status" class="btn btn-warning text-dark fw-bold">
                                    <i class="bi bi-door-closed-fill me-1"></i> FECHAR INSCRIÇÕES E ABRIR CHECK-IN
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary" disabled>Status não alterável por aqui</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span class="text-primary"><i class="bi bi-person-lines-fill me-2"></i> Jogadores Inscritos</span>
                <div>
                    <span class="badge bg-success me-1" title="Confirmados"><?php echo $camp['total_confirmados']; ?> pagos</span>
                    <span class="badge bg-secondary" title="Total Geral"><?php echo $camp['total_inscritos_geral']; ?> total</span>
                </div>
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
                            <?php if (empty($listaInscritos)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Nenhum jogador inscrito ainda.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($listaInscritos as $inscrito): ?>
                                    <?php
                                    // Badges de status do jogador
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
                                                <form method="POST" onsubmit="return confirm('Confirmar o recebimento do pagamento de <?php echo htmlspecialchars($inscrito['nickname']); ?>?');">
                                                    <input type="hidden" name="inscricao_id" value="<?php echo $inscrito['inscricao_id']; ?>">
                                                    <button type="submit" name="acao_simular_pagamento" class="btn btn-sm btn-outline-success fw-bold">
                                                        <i class="bi bi-cash-coin"></i> Confirmar Pagto
                                                    </button>
                                                </form>
                                            <?php elseif ($inscrito['status'] === 'confirmado' || $inscrito['status'] === 'checkin_realizado'): ?>
                                                <span class="text-success small fw-bold"><i class="bi bi-check-all"></i> Pago</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>