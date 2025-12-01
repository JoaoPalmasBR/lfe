<?php
// Arquivo: /pages/partida.php
// Objetivo: Vitrine pública de uma partida (estilo "X1" / Face-off).
// URL pública: seusite.com/pages/partida.php?id=X

// Tenta pegar o ID da partida da URL
$partidaId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Partida | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-p1: #0d6efd;
            /* Azul Bootstrap */
            --color-p2: #dc3545;
            /* Vermelho Bootstrap */
            --bg-dark: #121212;
        }

        body {
            background-color: var(--bg-dark);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Fonte de impacto para nomes e títulos */
        .font-impact {
            font-family: 'Teko', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Loader centralizado */
        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-dark);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Container principal da vitrine */
        .versus-container {
            min-height: 85vh;
            /* Ocupa quase toda a altura */
            display: flex;
            position: relative;
            overflow: hidden;
            border-bottom: 5px solid #333;
        }

        /* Áreas dos Jogadores */
        .player-stage {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            /* Nome fica embaixo */
            padding: 2rem;
            /* Máscara para escurecer a imagem e destacar o texto */
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.8) 100%);
        }

        /* Imagem de fundo do jogador */
        .player-bg-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center top;
            z-index: -1;
            transition: transform 0.3s ease;
        }

        /* Efeito de zoom suave ao passar o mouse */
        .player-stage:hover .player-bg-img {
            transform: scale(1.03);
        }

        /* Bordas coloridas inclinadas */
        .stage-p1 {
            border-right: 4px solid var(--color-p1);
        }

        .stage-p2 {
            border-left: 4px solid var(--color-p2);
        }

        /* Nickname gigante */
        .player-nick {
            font-size: 4rem;
            line-height: 1;
            margin-bottom: 0;
            text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.8);
        }

        .nick-p1 {
            color: var(--color-p1);
        }

        .nick-p2 {
            color: var(--color-p2);
        }

        /* O elemento "VS" central */
        .versus-separator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) skewX(-10deg);
            background: #fff;
            color: var(--bg-dark);
            font-size: 3.5rem;
            font-weight: 900;
            padding: 0.5rem 2rem;
            z-index: 10;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
            font-family: 'Teko', sans-serif;
        }

        /* Responsividade para Celular */
        @media (max-width: 768px) {
            .versus-container {
                flex-direction: column;
                min-height: auto;
            }

            .player-stage {
                height: 45vh;
                border: none;
                border-bottom: 4px solid #333;
            }

            .stage-p1 {
                border-color: var(--color-p1);
            }

            .stage-p2 {
                border-color: var(--color-p2);
            }

            .player-nick {
                font-size: 3rem;
            }

            .versus-separator {
                font-size: 2rem;
                padding: 0.2rem 1.5rem;
                /* Ajusta posição do VS no mobile */
                top: 45vh;
                /* Fica na junção dos dois */
            }
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <nav class="navbar navbar-dark bg-black py-3 border-bottom border-secondary">
        <div class="container-fluid px-4 justify-content-center justify-content-md-between">
            <a class="navbar-brand fw-bold font-impact fs-3" href="/">
                <i class="bi bi-controller me-2 text-primary"></i> LFE Arena
            </a>
            <a href="/campeonatos" class="btn btn-sm btn-outline-light d-none d-md-inline-block">
                <i class="bi bi-arrow-left me-2"></i> Ver Outros Campeonatos
            </a>
        </div>
    </nav>

    <div id="loading-overlay">
        <div class="text-center">
            <div class="spinner-grow text-primary mb-3" role="status" style="width: 3rem; height: 3rem;"></div>
            <p class="font-impact fs-4 text-uppercase ls-2">Carregando Confronto...</p>
        </div>
    </div>

    <main class="flex-shrink-0 d-none" id="main-content">

        <div class="bg-black py-3 text-center border-bottom border-dark">
            <h5 class="text-uppercase text-muted mb-1 font-impact ls-1" id="camp-nome">Nome do Campeonato</h5>
            <div class="badge bg-primary font-impact fs-5 px-4 py-2" id="rodada-label">Rodada X</div>
        </div>

        <div class="versus-container">
            <div class="versus-separator">VS</div>

            <div class="player-stage stage-p1 text-start">
                <div class="player-bg-img" id="bg-p1"></div>
                <div class="position-relative">
                    <span class="badge bg-primary mb-2">JOGADOR 1</span>
                    <h1 class="player-nick nick-p1 font-impact" id="nick-p1">Nome J1</h1>
                </div>
            </div>

            <div class="player-stage stage-p2 text-end">
                <div class="player-bg-img" id="bg-p2"></div>
                <div class="position-relative">
                    <span class="badge bg-danger mb-2">JOGADOR 2</span>
                    <h1 class="player-nick nick-p2 font-impact" id="nick-p2">Nome J2</h1>
                </div>
            </div>
        </div>

    </main>

    <div id="error-container" class="container text-center py-5 d-none">
        <i class="bi bi-exclamation-octagon-fill text-danger display-1 mb-4"></i>
        <h2 class="font-impact">Partida Não Encontrada</h2>
        <p class="lead text-muted" id="error-msg">O confronto que você procura não existe ou está indisponível.</p>
        <a href="/" class="btn btn-outline-light mt-4">Voltar ao Início</a>
    </div>


    <script>
        // Substitua pela URL real da sua API se necessário, ou mantenha dinâmico
        window.LFE_CONFIG = {
            API_URL: 'https://api.lfe.joao.palmas.br/v1'
        };
        const partidaId = <?php echo json_encode($partidaId); ?>;
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!partidaId) {
                showError("ID da partida não fornecido na URL.");
                return;
            }
            carregarPartida(partidaId);
        });

        async function carregarPartida(id) {
            try {
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/partida_detalhes.php?id=${id}`);
                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    popularTela(result.data);
                    // Esconde loader e mostra conteúdo
                    document.getElementById('loading-overlay').classList.add('d-none');
                    document.getElementById('main-content').classList.remove('d-none');
                } else {
                    throw new Error(result.message || 'Erro ao buscar dados.');
                }
            } catch (error) {
                console.error('Erro:', error);
                showError('Não foi possível carregar os detalhes deste confronto.');
            }
        }

        function popularTela(data) {
            // Topo
            document.getElementById('camp-nome').textContent = data.campeonato_nome;
            document.getElementById('rodada-label').textContent = data.rodada_label;

            // Jogador 1
            document.getElementById('nick-p1').textContent = data.j1_nick;
            // Define a imagem como background-image
            document.getElementById('bg-p1').style.backgroundImage = `url('${data.j1_foto}')`;

            // Jogador 2
            document.getElementById('nick-p2').textContent = data.j2_nick;
            document.getElementById('bg-p2').style.backgroundImage = `url('${data.j2_foto}')`;
        }

        function showError(msg) {
            document.getElementById('loading-overlay').classList.add('d-none');
            document.getElementById('main-content').classList.add('d-none');
            document.getElementById('error-container').classList.remove('d-none');
            document.getElementById('error-msg').textContent = msg;
        }
    </script>
</body>

</html>