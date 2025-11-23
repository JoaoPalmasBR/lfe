<?php
// O "Cérebro" Roteador (Atualizado para suportar pasta /painel/)

// --- LINHAS DE DEPURAÇÃO (REMOVER EM PRODUÇÃO) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------------------------

// 1. Carrega as configurações
require_once 'config_front.php';

// 2. Captura a URL digitada
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$urlParts = explode('/', $url);

// Variáveis de controle
$estadoSlugAtual = null;
$arquivoParaCarregar = ''; // O caminho físico do arquivo que vamos incluir

$primeiroPedaco = isset($urlParts[0]) && !empty($urlParts[0]) ? strtolower($urlParts[0]) : 'home';

// --- LÓGICA CENTRAL DE ROTEAMENTO ---

if ($primeiroPedaco === 'painel') {
    // >>> ROTA 1: ÁREA LOGADA (/painel) <<<
    // Se a URL começa com /painel, procuramos o index.php dentro da pasta física /painel/ na raiz.
    // Nota: No futuro, se tivermos /painel/meus-jogos, teremos que melhorar essa lógica aqui.
    // Por enquanto, qualquer coisa que comece com /painel carrega o index do painel.
    $arquivoParaCarregar = ROOT_PATH . '/painel/index.php';
} elseif (strlen($primeiroPedaco) === 2) {
    // >>> ROTA 2: ÁREA ESTADUAL (/to, /go, etc) <<<
    // Se são 2 letras, assumimos que é um estado.
    $estadoSlugAtual = $primeiroPedaco;
    // O segundo pedaço é a página (ex: campeonatos), se não tiver, é a home_estado.
    $pagina = isset($urlParts[1]) && !empty($urlParts[1]) ? $urlParts[1] : 'home_estado';
    $arquivoParaCarregar = ROOT_PATH . '/pages/' . $pagina . '.php';
} else {
    // >>> ROTA 3: ÁREA GLOBAL (/campeonatos, /login, /cadastro ou a home raiz) <<<
    $estadoSlugAtual = null;
    // Se for a raiz (home), carrega home_global, senão carrega o nome da página.
    $pagina = ($primeiroPedaco === 'home') ? 'home_global' : $primeiroPedaco;
    $arquivoParaCarregar = ROOT_PATH . '/pages/' . $pagina . '.php';
}

// Define a constante para uso no resto do site
define('ESTADO_ATUAL', $estadoSlugAtual);

// --- 4. CARREGAMENTO FINAL DO ARQUIVO ---
if (file_exists($arquivoParaCarregar)) {
    // O arquivo existe, vamos carregá-lo.
    require_once $arquivoParaCarregar;
} else {
    // O arquivo não existe. Mostra erro 404.
    http_response_code(404);

    // Mensagem de erro personalizada dependendo de onde estamos
    if ($primeiroPedaco === 'painel') {
        echo "<h1>Erro 404 (Painel)</h1><p>Arquivo principal da área logada não encontrado.</p>";
    } else {
        $paginaErro = isset($pagina) ? $pagina : $primeiroPedaco;
        echo "<h1>Erro 404</h1><p>Página pública não encontrada: " . htmlspecialchars($paginaErro) . ".php</p>";
    }
}
