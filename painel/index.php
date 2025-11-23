<?php
// Arquivo: panel/index.php (Teste do Guardião)

// 1. Carrega configurações e funções básicas
require_once __DIR__ . '/../config_front.php';
require_once __DIR__ . '/../includes/functions.php';

// 2. 🔐 CARREGA O GUARDIÃO (A linha mágica)
// Se não estiver logado, o script morre aqui e redireciona.
require_once __DIR__ . '/../includes/auth_guard.php';

// Se chegou aqui, o usuário está logado!
// A variável $currentUserFront está disponível.
?>
<h1>Painel Protegido</h1>
<p>Se você está vendo isso, o login funcionou.</p>

<h3>Dados do Usuário Logado (vindos da API):</h3>
<pre>
    <?php print_r($currentUserFront); ?>
</pre>

<a href="<?php echo BASE_URL; ?>/login">Voltar (Logout fake)</a>