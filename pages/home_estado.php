<?php
// Arquivo: pages/home_estado.php
// Objetivo: Página inicial pública ESPECÍFICA de um estado (ex: /to, /sp).
// Variável global disponível: $estadoSlugAtual (ex: 'to')

require_once ROOT_PATH . '/includes/functions.php';

// Garante que temos um estado selecionado, senão manda pra home global
if (!$estadoSlugAtual) {
    header("Location: " . BASE_URL);
    exit;
}

$estadoSiglaUpper = strtoupper($estadoSlugAtual);

// Verifica login para botões do menu
$usuarioLogadoHome = isset($_COOKIE['lfe_token']) && !empty($_COOKIE['lfe_token']);
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LFE Arena | <?php echo $estadoSiglaUpper; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* Mesmos estilos da Home Global para consistência */
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6f42c1;
            --bg-dark: #0a0a0a;
            --bg-card: #141414;
            --text-light: #e0e0e0;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .font-impact {
            font-family: 'Teko', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-home {
            background: rgba(0, 0, 0, 0.8) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Hero Section Regional */
        .hero-section {
            /* Imagem de fundo diferente para diferenciar da global */
            background: linear-gradient(rgba(0, 0, 0, 0.7), var(--bg-dark)), url('https://placehold.co/1920x600/2a0a0a/FFF?text=Federacao+<?php echo $estadoSiglaUpper; ?>');
            background-size: cover;
            background-position: center;
            padding: 120px 0 80px;
            border-bottom: 3px solid var(--primary-color);
        }

        .hero-title {
            font-size: 4rem;
            line-height: 1;
            /* Gradiente ligeiramente diferente */
            background: linear-gradient(45deg, #fff, #dc3545);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 3rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
        }

        .card-camp-home {
            background: var(--bg-card);
            border: 1px solid #222;
            transition: transform 0.3s, border-color 0.3s;
        }

        .card-camp-home:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
        }

        .table-dark-custom {
            --bs-table-bg: var(--bg-card);
            --bs-table-border-color: #333;
            color: var(--text-light);
        }

        .rank-pos-1 {
            color: #FFD700;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top navbar-home">
        <div class="container">
            <a class="navbar-brand font-impact fs-4" href="/"><i class="bi bi-chevron-left me-2 small"></i> LFE Global</a>
            <span class="navbar-text font-impact fs-4 text-white mx-3">|</span>
            <span class="navbar-brand font-impact fs-3 text-primary">Arena <?php echo $estadoSiglaUpper; ?></span>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="/<?php echo $estadoSlugAtual; ?>">Home <?php echo $estadoSiglaUpper; ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="/<?php echo $estadoSlugAtual; ?>/campeonatos">Torneios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/<?php echo $estadoSlugAtual; ?>/ranking">Ranking</a></li>
                    <li class="nav-item ms-lg-3">
                        <?php if ($usuarioLogadoHome): ?>
                            <a href="/painel" class="btn btn-primary fw-bold btn-sm px-4">Meu Painel</a>
                        <?php else: ?>
                            <a href="/login" class="btn btn-outline-light fw-bold btn-sm px-4">Login / Cadastro</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="hero-title font-impact mb-3">Federação de E-Sports do <?php echo $estadoSiglaUpper; ?></h1>
            <p class="lead mb-5 opacity-75 fs-4">O palco central para os competidores da nossa região.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="/<?php echo $estadoSlugAtual; ?>/campeonatos" class="btn btn-primary btn-lg fw-bold px-5 py-3">
                    <i class="bi bi-trophy-fill me-2"></i> Torneios em <?php echo $estadoSiglaUpper; ?>
                </a>
                <a href="/<?php echo $estadoSlugAtual; ?>/ranking" class="btn btn-outline-light btn-lg fw-bold px-5 py-3">
                    <i class="bi bi-bar-chart-fill me-2"></i> Ranking Estadual
                </a>
            </div>
        </div>
    </header>

    <section class="py-5">
        <div class="container py-4">
            <h2 class="text-center font-impact section-title display-5">Próximos em <?php echo $estadoSiglaUpper; ?></h2>

            <div id="featured-tournaments-loader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>

            <div class="row g-4 justify-content-center" id="featured-tournaments-container">
            </div>
        </div>
    </section>

    <section class="py-5 bg-card-dark" style="background: #0f0f0f;">
        <div class="container py-4">
            <h2 class="text-center font-impact section-title display-5">Top 5 <?php echo $estadoSiglaUpper; ?></h2>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-camp-home border-0 shadow-lg">
                        <div class="card-body p-0">
                            <div id="ranking-loader" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover table-dark-custom align-middle mb-0 d-none" id="ranking-table">
                                    <thead>
                                        <tr class="text-uppercase small text-muted">
                                            <th class="ps-4 py-3">Pos</th>
                                            <th>Jogador</th>
                                            <th class="text-end pe-4">Pontos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ranking-tbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="/<?php echo $estadoSlugAtual; ?>/ranking" class="btn btn-link text-decoration-none text-primary fw-bold">
                            Ver Ranking Completo de <?php echo $estadoSiglaUpper; ?> <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-black py-4 mt-auto border-top border-dark text-center text-muted small">
        <div class="container">
            <p class="mb-0 font-impact fs-5 text-white">LFE <?php echo $estadoSiglaUpper; ?> &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>


    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>'
        };
        // Passamos a sigla atual para o JS
        const currentSlug = '<?php echo $estadoSlugAtual; ?>';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadStateTournaments();
            loadStateRanking();
        });

        // 1. Busca Torneios DO ESTADO (Usando o novo filtro da API)
        async function loadStateTournaments() {
            const container = document.getElementById('featured-tournaments-container');
            const loader = document.getElementById('featured-tournaments-loader');

            try {
                // PASSANDO O FILTRO DE ESTADO NA URL
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/campeonatos.php?limite=3&estado_sigla=${currentSlug}`);
                const result = await response.json();

                if (response.ok && result.status === 'success' && result.data.length > 0) {
                    loader.classList.add('d-none');
                    result.data.slice(0, 3).forEach(camp => {
                        container.innerHTML += buildTournamentCard(camp);
                    });
                } else {
                    throw new Error('Nenhum torneio local.');
                }
            } catch (error) {
                loader.innerHTML = `<p class="text-muted py-3">Nenhum torneio agendado em ${currentSlug.toUpperCase()} no momento.</p>`;
            }
        }

        // 2. Busca Top Ranking DO ESTADO
        async function loadStateRanking() {
            const table = document.getElementById('ranking-table');
            const tbody = document.getElementById('ranking-tbody');
            const loader = document.getElementById('ranking-loader');

            try {
                // PASSANDO O FILTRO DE ESTADO NA URL
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/ranking.php?limite=5&estado_sigla=${currentSlug}`);
                const result = await response.json();

                if (response.ok && result.status === 'success' && result.data.ranking.length > 0) {
                    loader.classList.add('d-none');
                    table.classList.remove('d-none');

                    result.data.ranking.forEach(player => {
                        // Nota: Removemos a coluna de Estado da tabela pois já estamos na página do estado
                        tbody.innerHTML += `
                        <tr>
                            <td class="ps-4 fw-bold ${player.posicao === 1 ? 'rank-pos-1 fs-5' : ''}">#${player.posicao}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${player.avatar_url}" class="rounded-circle me-2" width="30" height="30">
                                    <span class="fw-bold">${player.nome_exibicao}</span>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold text-primary">${player.pontuacao_total}</td>
                        </tr>
                    `;
                    });
                } else {
                    throw new Error('Ranking vazio.');
                }
            } catch (error) {
                loader.innerHTML = `<p class="text-muted py-3">Ranking de ${currentSlug.toUpperCase()} ainda não disponível.</p>`;
            }
        }

        // Helper: Monta o card (igual ao global, mas o link leva para a área do estado)
        function buildTournamentCard(camp) {
            const dataInicio = new Date(camp.data_inicio_prevista).toLocaleDateString('pt-BR');
            const valor = camp.valor_inscricao > 0 ? `R$ ${parseFloat(camp.valor_inscricao).toFixed(2).replace('.', ',')}` : 'Grátis';

            return `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 card-camp-home text-white">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary">${camp.jogo}</span>
                            </div>
                        <h4 class="card-title font-impact mb-3 text-truncate">${camp.nome}</h4>
                        <ul class="list-unstyled small text-muted mb-4">
                             <li class="mb-2"><i class="bi bi-calendar-event me-2 text-primary"></i> Início: <strong>${dataInicio}</strong></li>
                             <li class="mb-2"><i class="bi bi-ticket-perforated me-2 text-primary"></i> Inscrição: <strong>${valor}</strong></li>
                        </ul>
                         <a href="/${currentSlug}/campeonatos" class="btn btn-primary fw-bold w-100">Ver Detalhes</a>
                    </div>
                </div>
            </div>
        `;
        }
    </script>
</body>

</html>