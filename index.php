<?php
// O "Cérebro" Roteador (Versão V2 - Suporte a sub-rotas no painel)

// --- LINHAS DE DEPURAÇÃO (REMOVER EM PRODUÇÃO) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------------------------

require_once 'config_front.php';

require_once ROOT_PATH . '/includes/functions.php';
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$urlParts = explode('/', $url);

$estadoSlugAtual = null;
$arquivoParaCarregar = '';

// Parâmetros extra que podem ser passados para a view (ex: ID do campeonato)
$routerParams = [];

$primeiroPedaco = isset($urlParts[0]) && !empty($urlParts[0]) ? strtolower($urlParts[0]) : 'home';
$segundoPedaco = isset($urlParts[1]) && !empty($urlParts[1]) ? strtolower($urlParts[1]) : null;
$terceiroPedaco = isset($urlParts[2]) && !empty($urlParts[2]) ? strtolower($urlParts[2]) : null;


// --- LÓGICA CENTRAL DE ROTEAMENTO V2 ---

if ($primeiroPedaco === 'painel') {
    // >>> ROTA 1: ÁREA LOGADA (/painel) <<<

    // 🔥 FIX CRÍTICO DE SEGURANÇA E ESCOPO 🔥
    // Carrega o guardião aqui para proteger TODAS as rotas internas do painel
    // e disponibilizar as variáveis $userTokenFront e $currentUserFront para as views.
    require_once ROOT_PATH . '/includes/auth_guard.php';
    if ($segundoPedaco === null) {
        // Acessou apenas /painel -> Carrega a home do painel
        $arquivoParaCarregar = ROOT_PATH . '/painel/index.php';
    } elseif ($segundoPedaco === 'campeonato' && is_numeric($terceiroPedaco)) {
        // Acessou /painel/campeonato/123 -> Carrega a visualização de detalhes
        // Passamos o ID (terceiro pedaço) via parâmetro
        $routerParams['id'] = $terceiroPedaco;
        $arquivoParaCarregar = ROOT_PATH . '/painel/views/gestor_campeonato_detalhes.php';
    } else {
        // Rota desconhecida dentro do painel
        $arquivoParaCarregar = '404_painel'; // Marcador para erro
    }
} elseif (strlen($primeiroPedaco) === 2) {
    // >>> ROTA 2: ÁREA ESTADUAL (/to, /go, etc) <<<
    $estadoSlugAtual = $primeiroPedaco;
    $pagina = ($segundoPedaco) ? $segundoPedaco : 'home_estado';
    $arquivoParaCarregar = ROOT_PATH . '/pages/' . $pagina . '.php';
} else {
    // >>> ROTA 3: ÁREA GLOBAL (/campeonatos, /login, etc) <<<
    $estadoSlugAtual = null;
    $pagina = ($primeiroPedaco === 'home') ? 'home_global' : $primeiroPedaco;
    $arquivoParaCarregar = ROOT_PATH . '/pages/' . $pagina . '.php';
}

define('ESTADO_ATUAL', $estadoSlugAtual);

// --- 4. CARREGAMENTO FINAL ---
if ($arquivoParaCarregar !== '404_painel' && file_exists($arquivoParaCarregar)) {
    require_once $arquivoParaCarregar;
} else {
    http_response_code(404);
    if ($primeiroPedaco === 'painel') {
        echo "<h1>Erro 404 (Painel)</h1><p>Página não encontrada dentro da área logada.</p>";
    } else {
        $paginaErro = isset($pagina) ? $pagina : $primeiroPedaco;
        echo "<h1>Erro 404</h1><p>Página pública não encontrada: " . htmlspecialchars($paginaErro) . "</p>";
    }
}
