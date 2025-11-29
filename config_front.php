<?php
// config_front.php

// 1. Defina a URL base do seu site (sem barra no final)
// SE ESTIVER NO SEU PC (LOCALHOST): Ajuste o caminho. Ex: 'http://localhost/meu_projeto_lfe'
// SE ESTIVER NO SERVIDOR REAL: Use 'https://lfe.esp.br'
// define('BASE_URL', 'http://localhost/lfe'); // <--- AJUSTE ESTA LINHA!
//define('BASE_URL', 'https://lfe.joao.palmas.br'); // <--- AJUSTE ESTA LINHA!
define('BASE_URL', 'https://lfe.esp.br'); // <--- AJUSTE ESTA LINHA!

// 2. Defina a URL base da API (sem barra no final)
// Geralmente é a BASE_URL + /v1
define('API_URL', 'https://api.lfe.joao.palmas.br/v1');
// define('API_URL', 'http://localhost/lfe_api/v1');

// 3. Defina o caminho físico da raiz do projeto no servidor
define('ROOT_PATH', __DIR__);
