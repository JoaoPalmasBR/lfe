<?php
// Arquivo: /painel/views/admin_dashboard.php
// View: Dashboard do Super Administrador (Gerenciar Estados)
// Variáveis disponíveis: $currentUserFront, $userTokenFront

// =====================================================================
// 1. PROCESSAR FORMULÁRIO (Criar Novo Estado)
// =====================================================================
$erroMsg = null;
$sucessoMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao_criar_estado'])) {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $sigla = strtoupper(filter_input(INPUT_POST, 'sigla', FILTER_SANITIZE_STRING));
    $slug = strtolower(filter_input(INPUT_POST, 'slug', FILTER_SANITIZE_STRING));
    // Repasses (opcionais no form, API usa padrão se vazio)
    $repasseEst = !empty($_POST['repasse_estadual']) ? (float)$_POST['repasse_estadual'] : null;
    $repassePlat = !empty($_POST['repasse_plataforma']) ? (float)$_POST['repasse_plataforma'] : null;

    if (empty($nome) || empty($sigla) || empty($slug)) {
        $erroMsg = "Nome, Sigla e Slug são obrigatórios.";
    } else {
        $payload = [
            'nome' => $nome,
            'sigla' => $sigla,
            'slug' => $slug,
            'repasse_estadual' => $repasseEst,
            'repasse_plataforma' => $repassePlat
        ];

        // Chama API de criação (POST)
        $apiResult = callAPI('POST', '/admin/estados.php', $payload, $userTokenFront);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            $sucessoMsg = "Estado '$nome' criado com sucesso!";
            // Limpa campos
            $nome = '';
            $sigla = '';
            $slug = '';
        } else {
            $erroMsg = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao criar estado.";
        }
    }
}

// =====================================================================
// 2. BUSCAR DADOS (Listar Estados Existentes)
// =====================================================================
$apiLista = callAPI('GET', '/admin/estados.php', null, $userTokenFront);
$listaEstados = ($apiLista['status'] === 'success') ? $apiLista['data'] : [];
?>
<?php
// Arquivo: /painel/views/jogador_dashboard.php
// View: Dashboard inicial do Jogador.
// Variáveis: $currentUserFront, $dadosView['inscricoes']
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Meu Painel</h1>
</div>

<div class="container-fluid px-4">
    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i> Cadastrar Nova Federação
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
                            <label class="form-label small fw-bold text-muted">Nome da Federação</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Liga Goiana de E-sports" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Sigla (UF)</label>
                                <input type="text" name="sigla" class="form-control text-uppercase" maxlength="2" placeholder="Ex: GO" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted">Slug na URL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light small">/</span>
                                    <input type="text" name="slug" class="form-control text-lowercase" placeholder="Ex: goias" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 text-muted opacity-25">
                        <p class="small text-muted fw-bold mb-3">Configuração Financeira (Opcional)</p>

                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label small text-muted">Repasse Estadual (%)</label>
                                <input type="number" step="0.01" name="repasse_estadual" class="form-control" placeholder="Pad: 20.00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small text-muted">Taxa Plataforma (%)</label>
                                <input type="number" step="0.01" name="repasse_plataforma" class="form-control" placeholder="Pad: 10.00">
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="acao_criar_estado" class="btn btn-primary fw-bold">
                                Criar Federação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-ul me-2"></i> Federações Cadastradas</span>
                    <span class="badge bg-secondary"><?php echo count($listaEstados); ?> total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="bg-light small text-muted text-uppercase">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Sigla</th>
                                    <th>Nome</th>
                                    <th>URL Amigável (Slug)</th>
                                    <th>Repasses (Est/Plat)</th>
                                    <th>Criado em</th>
                                </tr>
                            </thead>
                            <tbody class="small border-top-0">
                                <?php if (empty($listaEstados)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Nenhum estado cadastrado.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($listaEstados as $estado): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold">#<?php echo $estado['id']; ?></td>
                                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($estado['sigla']); ?></span></td>
                                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($estado['nome']); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL . '/' . htmlspecialchars($estado['slug']); ?>" target="_blank" class="text-decoration-none">
                                                    /<?php echo htmlspecialchars($estado['slug']); ?> <i class="bi bi-box-arrow-up-right small"></i>
                                                </a>
                                            </td>
                                            <td><?php echo $estado['percentual_repasse_estadual']; ?>% / <?php echo $estado['percentual_repasse_plataforma']; ?>%</td>
                                            <td class="text-muted"><?php echo formatarDataHora($estado['criado_em']); ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>