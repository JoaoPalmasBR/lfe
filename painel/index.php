<?php
// Arquivo: /painel/index.php
// Objetivo: Controlador principal da área logada. Decide qual visão carregar.

// 1. Carrega infraestrutura do front
require_once __DIR__ . '/../config_front.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. 🔐 GARANTE A SEGURANÇA (Redireciona se não logado)
require_once __DIR__ . '/../includes/auth_guard.php';
// Agora temos a variável global $currentUserFront com os dados do usuário.

// 3. Roteamento Interno do Painel (Decide qual VIEW carregar)
// Verifica a função do usuário para decidir o que mostrar.

$funcaoUsuario = $currentUserFront['funcao'];
$viewParaCarregar = '';

// Dados que serão passados para as views
$dadosView = [];

switch ($funcaoUsuario) {
    // --- ÁREA DO JOGADOR ---
    case 'jogador':
        // Antes de carregar a view, busca os dados necessários na API
        $apiResult = callAPI('GET', '/user/minhas_inscricoes.php', null, $userTokenFront);
        $dadosView['inscricoes'] = ($apiResult['status'] === 'success') ? $apiResult['data'] : [];

        $viewParaCarregar = 'jogador_dashboard.php';
        break;

    // --- ÁREA DO GESTOR ESTADUAL E STAFF ---
    case 'gestor_estadual':
    case 'staff':
        // Por enquanto, placeholder
        $dadosView['mensagem'] = "Bem-vindo à gestão do seu estado.";
        $viewParaCarregar = 'gestor_dashboard_placeholder.php';
        break;

    // --- ÁREA DO SUPER ADMIN ---
    case 'super_admin':
        // Por enquanto, placeholder
        $dadosView['mensagem'] = "Bem-vindo à visão global do sistema.";
        $viewParaCarregar = 'admin_dashboard_placeholder.php';
        break;

    default:
        // Função desconhecida (não deveria acontecer)
        die("Erro de perfil de usuário. Contate o suporte.");
}

// 4. Carrega o arquivo da VIEW correspondente
// As views usarão a variável $currentUserFront e o array $dadosView
require_once __DIR__ . '/views/' . $viewParaCarregar;
