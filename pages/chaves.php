<?php
// Arquivo: pages/chaves.php
// Objetivo: Visualização PÚBLICA das chaves (Solução Customizada JS/CSS).
require_once __DIR__ . '/../config_front.php';
$campeonatoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$campeonatoId) die('ID inválido');
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chaves do Torneio | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-card: #141414;
            --text-light: #e0e0e0;
            --primary: #0d6efd;
            --win-color: #198754;
            --border-color: #333;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', sans-serif;
        }

        .font-impact {
            font-family: 'Teko', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Container principal com scroll horizontal */
        .bracket-scroll-container {
            overflow-x: auto;
            padding: 40px 20px;
        }

        /* Layout flex para as colunas das rodadas */
        .bracket-wrapper {
            display: flex;
            flex-direction: row;
            gap: 40px;
        }

        /* Coluna da rodada: espaça as partidas verticalmente */
        .round-column {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            min-width: 220px;
        }

        /* Card da partida */
        .match-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin: 10px 0;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .match-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        /* Linhas de participante dentro do card */
        .player-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .player-row:last-child {
            border-bottom: none;
        }

        .player-name.winner {
            color: var(--win-color);
            font-weight: bold;
        }

        .player-score {
            font-weight: bold;
            margin-left: 10px;
        }

        .player-score.winner {
            background: var(--win-color);
            color: white;
            padding: 0 6px;
            border-radius: 4px;
        }

        /* --- CSS MÁGICO PARA AS LINHAS CONECTORAS --- */
        /* Adiciona um conector à direita de todas as partidas, exceto a última rodada (final) */
        .round-column:not(:last-child) .match-card::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -20px;
            /* Ponto médio à direita */
            width: 20px;
            height: 2px;
            background: var(--border-color);
        }

        /* Adiciona o conector vertical e a curva para as partidas pares (de baixo) que se juntam */
        .round-column:not(:first-child) .match-card:nth-child(even)::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20px;
            /* Começa na metade da partida de cima */
            width: 20px;
            height: 100%;
            /* Altura até a metade da partida atual */
            border-left: 2px solid var(--border-color);
            border-bottom: 2px solid var(--border-color);
            border-bottom-left-radius: 8px;
            /* Faz a curvinha */
        }

        /* Adiciona o conector reto para as partidas ímpares (de cima) */
        .round-column:not(:first-child) .match-card:nth-child(odd)::before {
            content: '';
            position: absolute;
            top: 50%;
            left: -20px;
            width: 20px;
            height: 2px;
            background: var(--border-color);
        }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-dark bg-black py-3 border-bottom border-secondary">
        <div class="container">
            <a class="navbar-brand font-impact fs-4" href="javascript:history.back()"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
            <span class="navbar-text font-impact fs-4 text-white" id="header-title">Carregando...</span>
        </div>
    </nav>

    <main class="flex-grow-1">
        <div class="container-fluid bracket-scroll-container">
            <div id="bracket-root" class="bracket-wrapper text-center">
                <div class="spinner-border text-primary m-5" role="status"></div>
            </div>
        </div>
    </main>

    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>',
            CAMP_ID: <?php echo $campeonatoId; ?>
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', initBracket);

        async function initBracket() {
            const root = document.getElementById('bracket-root');
            const header = document.getElementById('header-title');

            try {
                // 1. Busca os dados da API limpa
                const resp = await fetch(`${window.LFE_CONFIG.API_URL}/public/chaves_dados.php?campeonato_id=${window.LFE_CONFIG.CAMP_ID}`);
                const json = await resp.json();

                if (!resp.ok || json.status !== 'success') throw new Error(json.message || 'Erro na API');

                // 2. Atualiza cabeçalho
                header.textContent = `${json.data.meta.nome} [${json.data.meta.jogo}]`;

                // 3. Agrupa partidas por rodada
                const rounds = {};
                json.data.matches.forEach(m => {
                    if (!rounds[m.round]) rounds[m.round] = [];
                    rounds[m.round].push(m);
                });

                // 4. Gera o HTML
                let html = '';
                // Percorre as rodadas ordenadas (1, 2, 3...)
                Object.keys(rounds).sort((a, b) => a - b).forEach(roundNum => {
                    html += `<div class="round-column round-${roundNum}">`;
                    // Adiciona título da rodada (opcional)
                    // html += `<h5 class="text-center mb-4">Rodada ${roundNum}</h5>`; 
                    rounds[roundNum].forEach(match => {
                        html += renderMatchCard(match);
                    });
                    html += '</div>';
                });
                root.innerHTML = html;

            } catch (err) {
                root.innerHTML = `<div class="alert alert-danger m-5">${err.message}</div>`;
                header.textContent = 'Erro';
            }
        }

        // Função que desenha o bloco de uma partida
        function renderMatchCard(m) {
            const p1 = m.player1 || {
                name: 'A Definir',
                score: '-',
                winner: false
            };
            const p2 = m.player2 || {
                name: 'A Definir',
                score: '-',
                winner: false
            };

            // Função auxiliar para desenhar a linha de um jogador
            const renderPlayer = (p) => `
            <div class="player-row">
                <span class="player-name text-truncate ${p.winner ? 'winner' : ''}" title="${p.name}">${p.name}</span>
                <span class="player-score ${p.winner ? 'winner' : ''}">${p.score !== null ? p.score : '-'}</span>
            </div>`;

            // Retorna o card completo com link para a partida
            return `
        <div class="match-card shadow-sm" onclick="window.location.href='/pages/partida.php?id=${m.id}'">
            ${renderPlayer(p1)}
            ${renderPlayer(p2)}
        </div>`;
        }
    </script>
</body>

</html>