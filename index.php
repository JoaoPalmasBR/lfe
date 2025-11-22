<?php
// O "Cérebro" Roteador

// 1. Carrega as configurações
require_once 'config_front.php';

// 2. Captura a URL digitada (que o .htaccess enviou)
// Se não tiver nada, assume que é a página inicial.
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';

// Divide a URL em pedaços usando a barra '/'
$urlParts = explode('/', $url);

// Variáveis globais que controlam onde estamos
$estadoSlugAtual = null; // Se continuar null, estamos no site global
$paginaParaCarregar = '';

// 3. Lógica de Roteamento Simplificada (Para V1)
// Vamos assumir que se o primeiro pedaço da URL tiver 2 letras (ex: 'to', 'go'), é um estado.

$primeiroPedaco = isset($urlParts[0]) && !empty($urlParts[0]) ? strtolower($urlParts[0]) : 'home';

// Verifica se o primeiro pedaço parece ser uma sigla de estado (2 letras)
// NOTA: Num sistema real, perguntaríamos à API se esse slug é válido. Para simplificar agora, checamos o tamanho.
if (strlen($primeiroPedaco) === 2) {
    // --- ESTAMOS DENTRO DE UM ESTADO (Ex: lfe.esp.br/to/...) ---
    $estadoSlugAtual = $primeiroPedaco;

    // A página será o segundo pedaço (ex: 'campeonatos' em /to/campeonatos)
    // Se não tiver segundo pedaço, é a home do estado.
    $paginaParaCarregar = isset($urlParts[1]) && !empty($urlParts[1]) ? $urlParts[1] : 'home_estado';
} elseif ($primeiroPedaco === 'painel') {
    // --- ÁREA DO JOGADOR (Ex: lfe.esp.br/painel) ---
    // Por enquanto, vamos só carregar um arquivo placeholder
    $paginaParaCarregar = 'painel_placeholder';
} else {
    // --- ESTAMOS NO SITE GLOBAL (Ex: lfe.esp.br/campeonatos ou apenas lfe.esp.br) ---
    $estadoSlugAtual = null;

    if ($primeiroPedaco === 'home') {
        $paginaParaCarregar = 'home_global'; // Sua landing page antiga
    } else {
        $paginaParaCarregar = $primeiroPedaco;
    }
}

// Define uma constante para facilitar a verificação em outras partes do site
define('ESTADO_ATUAL', $estadoSlugAtual); // Será 'to' ou NULL


// 4. Carregar o arquivo da página correspondente
$arquivoPagina = ROOT_PATH . '/pages/' . $paginaParaCarregar . '.php';

if (file_exists($arquivoPagina)) {
    // Se a página existe na pasta /pages/, carrega ela.
    require_once $arquivoPagina;
} else {
    // Se não existe, mostra um erro 404 básico.
    http_response_code(404);
    echo "<h1>Erro 404</h1><p>Página não encontrada: " . htmlspecialchars($paginaParaCarregar) . "</p>";
    // Em produção, você carregaria require_once ROOT_PATH . '/pages/404.php';
}
