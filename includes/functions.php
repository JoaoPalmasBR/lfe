<?php
// Arquivo: includes/functions.php
// Funções auxiliares para o frontend

/**
 * Faz uma requisição HTTP para a nossa API.
 *
 * @param string $method GET, POST, PUT, etc.
 * @param string $endpoint O caminho da rota (ex: '/public/campeonatos.php')
 * @param array|null $data Dados para enviar no corpo (para POST/PUT)
 * @param string|null $token Token Bearer (para rotas protegidas)
 * @return array Retorna a resposta da API decodificada como um array associativo.
 */
function callAPI($method, $endpoint, $data = null, $token = null)
{
    // Monta a URL completa usando a constante definida no config_front.php
    $url = API_URL . $endpoint;

    // Inicializa o cURL
    $curl = curl_init($url);

    // Configurações padrão
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Retornar o resultado como string em vez de imprimir
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method); // Define o método (GET, POST, etc.)
    curl_setopt($curl, CURLOPT_TIMEOUT, 10); // Timeout de 10 segundos

    // Cabeçalhos HTTP
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];

    // Se tiver token, adiciona no cabeçalho
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    // Se tiver dados (para POST/PUT), codifica em JSON e envia
    if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    // --- IMPORTANTE PARA DESENVOLVIMENTO LOCAL (HTTPS) ---
    // Se você estiver usando HTTPS local sem certificado válido, descomente as linhas abaixo:
    // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    // -----------------------------------------------------

    // Executa a requisição
    $responseRaw = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $curlError = curl_error($curl);

    curl_close($curl);

    // Verifica se houve erro de conexão no cURL
    if ($curlError) {
        // Retorna um erro simulado da API para o front lidar
        return [
            'status' => 'error',
            'message' => 'Erro de conexão com o servidor: ' . $curlError,
            'code' => 'CURL_ERROR'
        ];
    }

    // Decodifica o JSON da resposta da API
    $responseDecoded = json_decode($responseRaw, true);

    // Se o JSON for inválido, retorna erro
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'status' => 'error',
            'message' => 'Erro ao processar resposta do servidor (JSON inválido).',
            'http_code' => $httpCode
        ];
    }

    return $responseDecoded;
}

/**
 * Função auxiliar para formatar moeda (BRL)
 */
function formatarMoeda($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Função auxiliar para formatar data (dd/mm/YYYY HH:ii)
 */
function formatarDataHora($dataSql)
{
    return date('d/m/Y H:i', strtotime($dataSql));
}
