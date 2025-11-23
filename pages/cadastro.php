<?php
// Arquivo: pages/cadastro.php
// Objetivo: Formulário público para criação de nova conta de JOGADOR.

require_once ROOT_PATH . '/includes/functions.php';

// Variáveis para feedback visual e repopulação do formulário
$erroCadastro = null;
$sucessoCadastro = false;
$nome = '';
$email = '';
$nickname = '';

// =====================================================================
// PROCESSAMENTO DO FORMULÁRIO (POST)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coletar e sanitizar dados (Corrigido para PHP 8.1+)
    $nome = isset($_POST['nome_completo']) ? trim($_POST['nome_completo']) : '';
    // O filtro de email continua válido e é bom manter
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $nickname = isset($_POST['nickname']) ? trim($_POST['nickname']) : '';
    $senha = $_POST['senha'] ?? '';

    // Validação básica antes de chamar API
    if (empty($nome) || empty($email) || empty($nickname) || empty($senha)) {
        $erroCadastro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (strlen($senha) < 6) {
        $erroCadastro = "A senha deve ter pelo menos 6 caracteres.";
    } else {
        // 2. Montar payload e Chamar API Pública de Registro
        $payload = [
            'nome_completo' => $nome,
            'email' => $email,
            'senha' => $senha,
            'nickname' => $nickname
        ];

        // Chama a rota pública que criamos anteriormente
        $apiResult = callAPI('POST', '/public/registro.php', $payload);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            // === SUCESSO ===
            $sucessoCadastro = true;
            // Limpa os campos para não mostrar no formulário
            $nome = '';
            $email = '';
            $nickname = '';
        } else {
            // === FALHA ===
            // Mostra a mensagem de erro vinda da API (ex: "E-mail já em uso")
            $erroCadastro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao criar conta. Tente novamente.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Conta | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-cadastro {
            max-width: 550px;
            margin: 50px auto;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-light bg-white shadow-sm mb-4">
        <div class="container text-center justify-content-center">
            <a class="navbar-brand fw-bold text-primary fs-3" href="<?php echo BASE_URL; ?>">LFE</a>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="card shadow border-0 card-cadastro">
            <div class="card-body p-4 p-md-5">

                <h3 class="fw-bold text-center mb-4">Crie sua Conta de Jogador</h3>

                <?php if ($sucessoCadastro): ?>
                    <div class="alert alert-success text-center" role="alert">
                        <h4 class="alert-heading"><i class="bi bi-check-circle-fill"></i> Conta Criada!</h4>
                        <p>Seu cadastro foi realizado com sucesso.</p>
                        <hr>
                        <div class="d-grid">
                            <a href="<?php echo BASE_URL; ?>/login" class="btn btn-success btn-lg fw-bold">
                                FAZER LOGIN AGORA <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                <?php elseif ($erroCadastro): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div><?php echo htmlspecialchars($erroCadastro); ?></div>
                    </div>
                <?php endif; ?>


                <?php if (!$sucessoCadastro): ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nome_completo" class="form-label fw-bold small">Nome Completo</label>
                            <input type="text" class="form-control" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($nome); ?>" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nickname" class="form-label fw-bold small">Nickname (Nome de Jogo)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">@</span>
                                    <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Ex: ProPlayer99" value="<?php echo htmlspecialchars($nickname); ?>" required>
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem">Será usado nos campeonatos.</small>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <label for="email" class="form-label fw-bold small">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="senha" class="form-label fw-bold small">Senha</label>
                            <input type="password" class="form-control" id="senha" name="senha" minlength="6" required>
                            <small class="text-muted" style="font-size: 0.75rem">Mínimo de 6 caracteres.</small>
                        </div>

                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold py-3">CRIAR CONTA</button>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if (!$sucessoCadastro): ?>
                    <div class="text-center text-muted small">
                        Já tem uma conta?
                        <a href="<?php echo BASE_URL; ?>/login" class="text-decoration-none fw-bold">Fazer Login</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>