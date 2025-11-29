<?php
// Arquivo: /painel/index.php
// Objetivo: Controlador principal da área logada. Roteia para views específicas e MONTA O LAYOUT.
// VERSÃO: COM LAYOUT PADRÃO (Header/Navbar/Footer)

// 1. Carrega infraestrutura do front
require_once __DIR__ . '/../config_front.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. 🔐 GARANTE A SEGURANÇA
require_once __DIR__ . '/../includes/auth_guard.php';
// Variável global disponível: $currentUserFront

$funcaoUsuario = $currentUserFront['funcao'];
$dadosView = [];
$viewParaCarregar = '';

// 3. ROTEAMENTO INTERNO
$viewSolicitada = $_GET['view'] ?? null;

if ($viewSolicitada) {
    // A. Roteamento Específico (?view=...)
    $viewLimpa = preg_replace('/[^a-zA-Z0-9_]/', '', $viewSolicitada);
    $caminhoArquivo = __DIR__ . '/views/' . $viewLimpa . '.php';

    if (file_exists($caminhoArquivo)) {
        $viewParaCarregar = $viewLimpa . '.php';
    } else {
        // View de erro 404 interna (crie este arquivo se quiser uma página bonita)
        $viewParaCarregar = '404.php';
        // Se não tiver arquivo 404.php, vamos criar um conteúdo padrão abaixo
        if (!file_exists(__DIR__ . '/views/404.php')) {
            $dadosView['erro_msg'] = 'Tela não encontrada.';
        }
    }
} else {
    // B. Roteamento Padrão (Dashboard Inicial)
    switch ($funcaoUsuario) {
        case 'jogador':
            $apiResult = callAPI('GET', '/user/minhas_inscricoes.php', null, $userTokenFront);
            $dadosView['inscricoes'] = ($apiResult['status'] === 'success') ? $apiResult['data'] : [];
            $viewParaCarregar = 'jogador_dashboard.php';
            break;
        case 'gestor_estadual':
        case 'staff':
            $dadosView['mensagem'] = "Bem-vindo à gestão do seu estado.";
            $viewParaCarregar = 'gestor_dashboard.php';
            break;
        case 'super_admin':
            $dadosView['mensagem'] = "Bem-vindo à visão global do sistema.";
            $viewParaCarregar = 'admin_dashboard.php';
            break;
        default:
            die("Erro de perfil.");
    }
}

// ====================================================================
// 4. MONTAGEM DO LAYOUT (O Segredo da Solução!)
// ====================================================================

// 4.1. Carrega o Cabeçalho HTML e CSS
require_once __DIR__ . '/includes/header.php';

// 4.2. Carrega a Barra de Navegação (Menu)
require_once __DIR__ . '/includes/navbar.php';

// 4.3. Conteúdo Principal (Wrapper)
echo '<main class="flex-shrink-0 main-content mb-4">';
echo '<div class="container">'; // Container principal do Bootstrap

// --- CARREGA A VIEW ESPECÍFICA AQUI ---
if (!empty($viewParaCarregar) && file_exists(__DIR__ . '/views/' . $viewParaCarregar)) {
    require_once __DIR__ . '/views/' . $viewParaCarregar;
} elseif (isset($dadosView['erro_msg'])) {
    // Fallback simples se não tiver arquivo 404.php
    echo '<div class="alert alert-danger">Erro 404: ' . htmlspecialchars($dadosView['erro_msg']) . '</div>';
}
// --------------------------------------

echo '</div>'; // Fecha container
echo '</main>';

// 4.4. Carrega o Rodapé e Scripts JS
require_once __DIR__ . '/includes/footer.php';
