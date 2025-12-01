<?php
// Arquivo: pages/login.php
// Objetivo: Exibir formulário de login e processar a autenticação via API.
// =====================================================================
// 1. CARREGAMENTO ESSENCIAL (CORREÇÃO)
// =====================================================================
// Precisamos carregar as configurações ANTES de tentar usar BASE_URL ou ROOT_PATH.
// Usamos __DIR__ para voltar um nível e achar o config na raiz.
if (file_exists(__DIR__ . '/../config_front.php')) {
    require_once __DIR__ . '/../config_front.php';
} else {
    // Fallback de segurança caso a estrutura de pastas seja diferente
    die("Erro crítico: Arquivo de configuração não encontrado.");
}

// Carrega as funções auxiliares
require_once ROOT_PATH . '/includes/functions.php';

// =====================================================================
// 2. LÓGICA DE LOGOUT
// =====================================================================
// Verifica se o usuário clicou no botão "Sair" (que leva para ?logout=true)
if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Para "deletar" um cookie, nós o redefinimos com uma data de validade no passado.
    // Importante: O caminho '/' deve ser o mesmo usado quando o cookie foi criado.
    setcookie('lfe_token', '', time() - 3600, '/');

    // Após destruir o cookie, redirecionamos para a própria página de login limpa
    // Agora é seguro usar BASE_URL aqui.
    header("Location: " . BASE_URL . "/login?msg=logged_out");
    exit; // Interrompe o script aqui para não carregar o resto da página
}
// ===============================
$erroLogin = null;
$emailInformado = '';

// =====================================================================
// PROCESSAMENTO DO FORMULÁRIO (Se o usuário clicou em "Entrar")
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Coletar dados do formulário
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? ''; // Senha não se sanitiza, usa crua

    if (empty($email) || empty($senha)) {
        $erroLogin = "Por favor, preencha e-mail e senha.";
    } else {
        // 2. Chamar a API de Login
        $payload = ['email' => $email, 'senha' => $senha];
        $apiResult = callAPI('POST', '/auth/login.php', $payload);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            // === LOGIN SUCESSO ===
            $token = $apiResult['data']['token'];
            $expiraEm = strtotime($apiResult['data']['expira_em']); // Converte string data para timestamp

            // 3. Definir o Cookie Seguro (HttpOnly)
            // Nome: lfe_token
            // Valor: O token gigante
            // Expira: Data retornada pela API
            // Path: / (disponível em todo o site)
            // Domain: null (domínio atual)
            // Secure: true (Só envia se for HTTPS - Recomendado em produção)
            // HttpOnly: true (CRITICAL: JavaScript não acessa. Protege contra XSS)
            $cookieSeguro = setcookie(
                'lfe_token',
                $token,
                [
                    'expires' => $expiraEm,
                    'path' => '/',
                    // 'secure' => true, // Mantenha comentado se for localhost, descomente em produção SSL
                    'httponly' => false, // <--- MUDAMOS PARA FALSE
                    'samesite' => 'Lax'
                ]
            );

            if ($cookieSeguro) {
                // 4. Redirecionar para o Painel
                header("Location: " . BASE_URL . "/painel");
                exit;
            } else {
                $erroLogin = "Erro ao criar sessão segura no navegador.";
            }
        } else {
            // === LOGIN FALHOU ===
            $erroLogin = isset($apiResult['message']) ? $apiResult['message'] : "E-mail ou senha inválidos.";
            $emailInformado = $email; // Para repopular o campo
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .card-login {
            max-width: 450px;
            width: 100%;
            margin: auto;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card shadow-sm border-0 card-login">
            <div class="card-body p-5">

                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Acessar Conta</h3>
                    <p class="text-muted small">Entre para gerenciar suas inscrições.</p>
                </div>

                <?php if ($erroLogin): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div><?php echo htmlspecialchars($erroLogin); ?></div>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">E-mail</label>
                        <input type="email" class="form-label form-control form-control-lg" id="email" name="email" value="<?php echo htmlspecialchars($emailInformado); ?>" required autofocus>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label for="senha" class="form-label fw-bold small">Senha</label>
                        </div>
                        <input type="password" class="form-control form-control-lg" id="senha" name="senha" required>
                    </div>
                    <div class="d-grid gap-2 mb-4">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold py-3">ENTRAR</button>
                    </div>
                </form>

                <div class="text-center text-muted small">
                    Não tem uma conta?
                    <a href="<?php echo BASE_URL; ?>/cadastro" class="text-decoration-none fw-bold">Cadastre-se agora</a>
                    <br><br>
                    <a href="<?php echo BASE_URL; ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Voltar ao site</a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>