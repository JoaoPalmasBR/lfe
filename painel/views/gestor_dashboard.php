<?php
// Arquivo: /painel/views/gestor_dashboard.php
// View: Dashboard principal do Gestor Estadual
// Variáveis disponíveis: $currentUserFront, $userTokenFront

// =====================================================================
// 1. OBTER DADOS DO MEU ESTADO
// =====================================================================
$apiEstado = callAPI('GET', '/gestor/meu_estado.php', null, $userTokenFront);
$meuEstado = ($apiEstado['status'] === 'success') ? $apiEstado['data'] : ['nome' => 'Estado Desconhecido', 'sigla' => '??'];

// =====================================================================
// 2. PROCESSAR FORMULÁRIO (Criar Novo Campeonato)
// =====================================================================
$erroMsg = null;
$sucessoMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_criar_camp'])) {
    // Coleta dados básicos
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $dataRaw = $_POST['data_inicio'] ?? ''; // Vem do input type="datetime-local" (YYYY-MM-DDTHH:II)
    $valor = !empty($_POST['valor_inscricao']) ? (float)$_POST['valor_inscricao'] : 0.00;
    $limite = !empty($_POST['limite']) ? (int)$_POST['limite'] : 64;

    if (empty($nome) || empty($dataRaw)) {
        $erroMsg = "Nome e Data de Início são obrigatórios.";
    } else {
        // Formata a data para o padrão MySQL (substitui o T por espaço e adiciona segundos se precisar)
        $dataInicioSQL = str_replace('T', ' ', $dataRaw) . ':00';

        // Configuração de Premiação Dinâmica (Padrão simples para V1)
        // No futuro, faremos um UI para adicionar mais posições
        $configPremiacao = [
            "1º Lugar" => (int)($_POST['premio_1'] ?? 100),
            "2º Lugar" => (int)($_POST['premio_2'] ?? 0)
        ];
        // Remove se for zero para não sujar o JSON
        if ($configPremiacao["2º Lugar"] === 0) unset($configPremiacao["2º Lugar"]);

        $payload = [
            'nome' => $nome,
            'data_inicio' => $dataInicioSQL,
            'valor_inscricao' => $valor,
            'limite_participantes' => $limite,
            'config_premiacao' => $configPremiacao
        ];

        // Chama API de criação (POST)
        $apiResult = callAPI('POST', '/gestor/campeonatos.php', $payload, $userTokenFront);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $sucessoMsg = "Campeonato criado com sucesso (Rascunho)!";
            // Limpa campos principais para não duplicar fácil
            $nome = '';
            $dataRaw = '';
        } else {
            $erroMsg = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao criar campeonato.";
        }
    }
}

// =====================================================================
// 3. BUSCAR DADOS (Listar Meus Campeonatos)
// =====================================================================
$apiLista = callAPI('GET', '/gestor/campeonatos.php', null, $userTokenFront);
$listaCamp = ($apiLista['status'] === 'success') ? $apiLista['data'] : [];
?>
<?php
// Arquivo: /painel/views/jogador_dashboard.php
// View: Dashboard inicial do Jogador.
// Variáveis: $currentUserFront, $dadosView['inscricoes']
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Meu Painel</h1>
</div>

<div class="container-fluid px-4 pb-5">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3 text-primary">
                    <i class="bi bi-plus-square-fill me-2"></i> Criar Novo Campeonato
                </div>
                <div class="card-body">

                    <?php if ($erroMsg): ?>
                        <div class="alert alert-danger small py-2 mb-3"><?php echo htmlspecialchars($erroMsg); ?></div>
                    <?php endif; ?>
                    <?php if ($sucessoMsg): ?>
                        <div class="alert alert-success small py-2 mb-3"><?php echo htmlspecialchars($sucessoMsg); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nome do Evento</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Copa Estadual de Inverno" required value="<?php echo htmlspecialchars($nome ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Data e Hora de Início</label>
                            <input type="datetime-local" name="data_inicio" class="form-control" required value="<?php echo htmlspecialchars($dataRaw ?? ''); ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Inscrição (R$)</label>
                                <input type="number" step="0.01" name="valor_inscricao" class="form-control" placeholder="0.00" value="<?php echo htmlspecialchars($_POST['valor_inscricao'] ?? ''); ?>">
                                <small class="text-muted" style="font-size: 11px">Deixe 0 para gratuito.</small>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Limite Vagas</label>
                                <input type="number" name="limite" class="form-control" placeholder="Pad: 64" value="<?php echo htmlspecialchars($_POST['limite'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr class="my-3 text-muted opacity-25">
                        <p class="small text-muted fw-bold mb-2">Premiação (% do arrecadado)</p>
                        <div class="row mb-4 g-2">
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">1º (%)</span>
                                    <input type="number" name="premio_1" class="form-control" placeholder="100" min="1" max="100">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">2º (%)</span>
                                    <input type="number" name="premio_2" class="form-control" placeholder="0" min="0" max="100">
                                </div>
                            </div>
                            <small class="text-muted mt-1" style="font-size: 11px">O sistema calculará o valor em R$ no dia.</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="acao_criar_camp" class="btn btn-primary fw-bold">
                                Salvar Rascunho
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="mb-3 text-end">
                <a href="<?php echo BASE_URL; ?>/painel/checkin" class="btn btn-dark fw-bold shadow-sm">
                    <i class="bi bi-qr-code-scan me-2"></i> ABRIR TELA DE PORTARIA (CHECK-IN)
                </a>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span class="text-primary"><i class="bi bi-trophy-fill me-2"></i> Meus Campeonatos</span>
                    <span class="badge bg-primary opacity-75"><?php echo count($listaCamp); ?> total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-4">Status</th>
                                    <th>Nome</th>
                                    <th>Data Início</th>
                                    <th>Inscritos</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                <?php if (empty($listaCamp)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-emoji-frown fs-1 d-block mb-2"></i>
                                            Nenhum campeonato criado ainda.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($listaCamp as $camp): ?>
                                        <?php
                                        // Badges de status
                                        $badges = [
                                            'rascunho' => ['bg-secondary', 'Rascunho (Invisível)'],
                                            'inscricoes_abertas' => ['bg-success', 'Inscrições Abertas'],
                                            'checkin_aberto' => ['bg-warning text-dark', 'Check-in Aberto'],
                                            'em_andamento' => ['bg-danger', 'Em Andamento'],
                                            'finalizado' => ['bg-dark', 'Finalizado']
                                        ];
                                        $badge = $badges[$camp['status']] ?? ['bg-light text-dark', $camp['status']];
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <span class="badge <?php echo $badge[0]; ?> small"><?php echo $badge[1]; ?></span>
                                            </td>
                                            <td class="fw-bold"><?php echo htmlspecialchars($camp['nome']); ?></td>
                                            <td class="small text-muted"><?php echo formatarDataHora($camp['data_inicio_prevista']); ?></td>
                                            <td class="small">
                                                <i class="bi bi-people-fill text-muted"></i> 0 / <?php echo $camp['limite_participantes']; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/painel/campeonato/<?php echo $camp['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold">
                                                    Gerenciar <i class="bi bi-arrow-right-short"></i>
                                                </a>
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

    </div>
</div>