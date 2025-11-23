<?php
// Arquivo: includes/auth_guard.php
// Objetivo: Proteger páginas que exigem login. Inclua no topo dos arquivos.
// ATENÇÃO: Este arquivo depende que 'config_front.php' e 'functions.php' já tenham sido carregados antes dele.

// 1. Verifica se o cookie do token existe
if (!isset($_COOKIE['lfe_token']) || empty($_COOKIE['lfe_token'])) {
    // Cookie não existe. Usuário não está logado.
    header("Location: " . BASE_URL . "/login?msg=access_denied");
    exit; // Interrompe a execução da página imediatamente
}

$userTokenFront = $_COOKIE['lfe_token'];

// 2. Valida o token na API (Pergunta: "Quem é esse usuário?")
// Chama a rota que acabamos de criar
$apiAuthResult = callAPI('GET', '/user/me.php', null, $userTokenFront);

$currentUserFront = null; // Variável global que guardará os dados do usuário na página

if (isset($apiAuthResult['status']) && $apiAuthResult['status'] === 'success') {
    // === SUCESSO ===
    // O token é válido.
    // Salvamos os dados do usuário para usar na página (ex: mostrar o nome no topo)
    $currentUserFront = $apiAuthResult['data'];

    // O script termina aqui e deixa o restante da página (que incluiu este arquivo) carregar normalmente.

} else {
    // === FALHA ===
    // O cookie existe, mas a API disse que o token é inválido ou expirou.

    // 1. Destruir o cookie inválido (define data de expiração no passado)
    setcookie('lfe_token', '', time() - 3600, '/');

    // 2. Redirecionar para login com mensagem de sessão expirada
    header("Location: " . BASE_URL . "/login?msg=session_expired");
    exit;
}
