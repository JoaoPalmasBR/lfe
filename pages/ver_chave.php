<?php
// Arquivo: /pages/ver_chave.php
// Objetivo: Página pública para visualizar o chaveamento de um torneio.

// Pega o ID da URL (ex: ?id=6)
$campId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($campId === 0) {
    die("ID do campeonato não informado.");
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Chave do Torneio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* Variáveis para facilitar ajustes */
        :root {
            --bracket-bg: #f8f9fa;
            --card-width: 220px;
            --card-margin-y: 20px;
            --connector-width: 40px;
            /* Largura das linhas conectoras */
            --line-color: #adb5bd;
            /* Cor das linhas */
            --line-thickness: 2px;
            /* Espessura das linhas */
        }

        /* Wrapper principal para permitir scroll horizontal em telas pequenas */
        .bracket-wrapper {
            overflow-x: auto;
            padding: 40px 20px;
            background-color: var(--bracket-bg);
            min-height: 500px;
            border-radius: 8px;
        }

        /* O container Flex que segura as colunas das rodadas */
        .bracket-container {
            display: flex;
            flex-direction: row;
            /* Adiciona um espaço extra no final para a última rodada não ficar colada */
            padding-right: var(--connector-width);
        }

        /* Coluna de uma Rodada */
        .round-column {
            display: flex;
            flex-direction: column;
            /* O segredo do alinhamento: distribui o espaço verticalmente */
            justify-content: space-around;
            width: var(--card-width);
            /* Espaço à direita para caberem as linhas conectoras */
            margin-right: var(--connector-width);
            flex-shrink: 0;
            position: relative;
            /* Necessário para os conectores */
        }

        /* Remove a margem da última coluna (onde está o campeão) */
        .round-column:last-child {
            margin-right: 0;
        }

        /* Título da Rodada */
        .round-header {
            text-align: center;
            font-weight: bold;
            color: #6c757d;
            margin-bottom: 20px;
            text-transform: uppercase;
            font-size: 0.9rem;
            height: 30px;
            /* Altura fixa para ajudar no alinhamento */
        }

        /* O Card do Jogo (Match Box) */
        .match-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 10px;
            position: relative;
            margin-bottom: var(--card-margin-y);
            z-index: 2;
            /* Garante que o card fique sobre as linhas */
        }

        /* Estilos internos do card (jogadores, placar, etc) */
        .player-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }

        .player-row.winner {
            font-weight: bold;
            color: #198754;
        }

        .player-row.winner .score-box {
            background-color: #198754;
            color: white;
            border-color: #198754;
        }

        .player-name.tbd {
            color: #adb5bd;
            font-style: italic;
        }

        .score-box {
            background: #e9ecef;
            min-width: 24px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 2px 5px;
            font-weight: bold;
            margin-left: 10px;
            font-size: 0.9rem;
        }

        /* =================================================================
           ---- CONECTORES DA CHAVE (BRACKET LINES) ----
           A mágica do CSS para desenhar as linhas usando ::before e ::after
        ================================================================= */

        /* 1. CONECTOR DE SAÍDA (Direita do card)
           Aplica-se a todos os cards, EXCETO os da última rodada. */
        .round-column:not(:last-child) .match-card::after {
            content: '';
            position: absolute;
            right: calc(var(--connector-width) * -1);
            /* Sai do card para a direita */
            width: var(--connector-width);
            height: 50%;
            /* Altura inicial base */
            border-color: var(--line-color);
            border-style: solid;
            border-width: 0;
            z-index: 1;
            /* Fica atrás do card */
            pointer-events: none;
        }

        /* Lógica para jogos ÍMPARES na coluna (o "topo" de um par):
           A linha sai do meio, vai para a direita e DESCE. */
        .round-column:not(:last-child) .match-card:nth-of-type(odd)::after {
            top: 50%;
            border-right-width: var(--line-thickness);
            border-bottom-width: var(--line-thickness);
            /* O truque: a altura precisa ser grande o suficiente para encontrar o card de baixo */
            height: 100%;
            /* Ajuste fino para centralizar a linha horizontal */
            transform: translateY(calc(var(--line-thickness) / 2));
        }

        /* Lógica para jogos PARES na coluna (a "base" de um par):
           A linha sai do meio, vai para a direita e SOBE. */
        .round-column:not(:last-child) .match-card:nth-of-type(even)::after {
            bottom: 50%;
            border-right-width: var(--line-thickness);
            border-top-width: var(--line-thickness);
            height: 100%;
            transform: translateY(calc(var(--line-thickness) / -2));
        }

        /* 2. CONECTOR DE ENTRADA (Esquerda do card)
           Aplica-se a todos os cards, EXCETO os da primeira rodada.
           É o pequeno "rabicho" horizontal que recebe a linha da rodada anterior. */
        .round-column:not(:first-child) .match-card::before {
            content: '';
            position: absolute;
            left: calc(var(--connector-width) / -2 - var(--line-thickness));
            /* Posiciona à esquerda */
            top: 50%;
            /* A largura é metade do conector total + a espessura da linha vertical */
            width: calc(var(--connector-width) / 2 + var(--line-thickness));
            height: var(--line-thickness);
            background: var(--line-color);
            z-index: 1;
            transform: translateY(-50%);
            /* Centraliza verticalmente */
        }
    </style>
</head>

<body class="bg-light">

    <div class="container py-5">
        <h1 class="text-center fw-bold mb-4 text-primary">Chaveamento do Torneio</h1>

        <div id="bracket-loader" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Carregando chave...</p>
        </div>

        <div id="bracket-error" class="alert alert-danger d-none text-center"></div>

        <div id="bracket-area" class="bracket-wrapper d-none shadow-sm">
            <div id="bracket-flex-container" class="bracket-container">
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-secondary">Voltar ao Início</a>
        </div>

    </div>

    <script>
        // ATENÇÃO: Verifique se a URL da sua API está correta aqui!
        const API_BASE_URL = 'https://api.lfe.joao.palmas.br/v1';
        const CAMPEONATO_ID = <?php echo $campId; ?>;
    </script>

    <script src="/pages/js/bracket_render.js?v=<?php echo time(); ?>"></script>

</body>

</html>