<?php
// Arquivo: pages/home_global.php
// Objetivo: Página inicial pública global com menu de estados.
// VERSÃO: CORRIGIDA (Consumindo API em vez de DB direto)

require_once ROOT_PATH . '/includes/functions.php';

// 1. Verifica login (visual)
$usuarioLogadoHome = isset($_COOKIE['lfe_token']) && !empty($_COOKIE['lfe_token']);

// 2. Busca lista de estados para o menu dropdown VIA API
$listaEstadosMenu = [];
try {
    // Usa a função auxiliar do frontend para chamar a API
    $apiResult = callAPI('GET', '/public/estados.php');

    if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
        $listaEstadosMenu = $apiResult['data'];
    }
} catch (Exception $e) {
    // Silencia erro se a API falhar, para não quebrar a home inteira
    error_log("Erro ao chamar API de estados no front: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LFE Arena - Sua Plataforma de Competição</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
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

        /* Estilo para o Dropdown escuro */
        .dropdown-menu-dark-custom {
            background-color: rgba(20, 20, 20, 0.95);
            border: 1px solid #333;
        }

        .dropdown-menu-dark-custom .dropdown-item {
            color: var(--text-light);
        }

        .dropdown-menu-dark-custom .dropdown-item:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), var(--bg-dark)), url('../background-lfe.jpg');
            background-size: cover;
            background-position: center;
            padding: 150px 0 100px;
            border-bottom: 3px solid var(--primary-color);
        }

        .hero-title {
            font-size: 4.5rem;
            line-height: 1;
            background: linear-gradient(45deg, #fff, var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stats-banner {
            background: var(--bg-card);
            padding: 2rem 0;
            border-bottom: 1px solid #222;
        }

        .stat-number {
            font-size: 2.5rem;
            color: var(--primary-color);
            line-height: 1;
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
            <a class="navbar-brand font-impact fs-3" href="/"><i class="bi bi-controller me-2 text-primary"></i> LFE Arena</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>

                    <?php if (!empty($listaEstadosMenu)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownEstados" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Estados
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark-custom" aria-labelledby="navbarDropdownEstados">
                                <?php foreach ($listaEstadosMenu as $est): ?>
                                    <li><a class="dropdown-item font-impact ls-1" href="/<?php echo strtolower($est['sigla']); ?>">
                                            <?php echo htmlspecialchars($est['nome']); ?>
                                        </a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item"><a class="nav-link" href="/campeonatos">Torneios</a></li>
                    <li class="nav-item"><a class="nav-link" href="/ranking">Ranking</a></li>
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

    <header class="hero-section text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="hero-title font-impact mb-3">Sua Jornada Competitiva Começa Aqui.</h1>
                    <p class="lead mb-5 opacity-75 fs-4">Participe dos melhores torneios de e-sports do Brasil, suba no ranking e construa seu legado.</p>
                    <div class="d-flex gap-3 justify-content-center justify-content-lg-start">
                        <a href="/campeonatos" class="btn btn-primary btn-lg fw-bold px-5 py-3"><i class="bi bi-trophy-fill me-2"></i> Ver Torneios</a>
                        <?php if (!$usuarioLogadoHome): ?>
                            <a href="/cadastro" class="btn btn-outline-light btn-lg fw-bold px-5 py-3">Crie sua Conta</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="stats-banner font-impact">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="stat-number">+1.500</div>
                    <div class="text-uppercase ls-1 text-muted">Jogadores Registrados</div>
                </div>
                <div class="col-md-4">
                    <div class="stat-number">+50</div>
                    <div class="text-uppercase ls-1 text-muted">Torneios Realizados</div>
                </div>
                <div class="col-md-4">
                    <div class="stat-number">R$ 10K+</div>
                    <div class="text-uppercase ls-1 text-muted">Em Premiações</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container py-4">
            <h2 class="text-center font-impact section-title display-5">Próximos Torneios</h2>
            <div id="featured-tournaments-loader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
            <div class="row g-4 justify-content-center" id="featured-tournaments-container"></div>
            <div class="text-center mt-5"><a href="/campeonatos" class="btn btn-outline-primary fw-bold px-5">Ver Todos os Torneios</a></div>
        </div>
    </section>

    <section class="py-5 bg-card-dark" style="background: #0f0f0f;">
        <div class="container py-4">
            <h2 class="text-center font-impact section-title display-5">Top 5 Brasil</h2>
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
                                            <th class="text-center">Estado</th>
                                            <th class="text-end pe-4">Pontos</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ranking-tbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4"><a href="/ranking" class="btn btn-link text-decoration-none text-primary fw-bold">Ver Ranking Completo <i class="bi bi-arrow-right"></i></a></div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-black py-4 mt-auto border-top border-dark text-center text-muted small">
        <div class="container">
            <p class="mb-1 font-impact fs-5 text-white">LFE Arena &copy; <?php echo date('Y'); ?></p>
            <p>A plataforma definitiva para e-sports regionais.</p>
        </div>
    </footer>

    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>'
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadFeaturedTournaments();
            loadTopRanking();
        });
        async function loadFeaturedTournaments() {
            const container = document.getElementById('featured-tournaments-container');
            const loader = document.getElementById('featured-tournaments-loader');
            try {
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/campeonatos.php?limite=3`);
                const result = await response.json();
                if (response.ok && result.status === 'success' && result.data.length > 0) {
                    loader.classList.add('d-none');
                    result.data.slice(0, 3).forEach(camp => {
                        container.innerHTML += buildTournamentCard(camp);
                    });
                } else {
                    throw new Error('Nenhum torneio encontrado.');
                }
            } catch (error) {
                loader.innerHTML = '<p class="text-muted">Nenhum torneio em destaque no momento.</p>';
            }
        }
        async function loadTopRanking() {
            const table = document.getElementById('ranking-table');
            const tbody = document.getElementById('ranking-tbody');
            const loader = document.getElementById('ranking-loader');
            try {
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/ranking.php?limite=5`);
                const result = await response.json();
                if (response.ok && result.status === 'success' && result.data.ranking.length > 0) {
                    loader.classList.add('d-none');
                    table.classList.remove('d-none');
                    result.data.ranking.forEach(player => {
                        tbody.innerHTML += `<tr><td class="ps-4 fw-bold ${player.posicao === 1 ? 'rank-pos-1 fs-5' : ''}">#${player.posicao}</td><td><div class="d-flex align-items-center"><img src="${player.avatar_url}" class="rounded-circle me-2" width="30" height="30"><span class="fw-bold">${player.nome_exibicao}</span></div></td><td class="text-center"><small class="badge bg-dark border">${player.estado_sigla}</small></td><td class="text-end pe-4 fw-bold text-primary">${player.pontuacao_total}</td></tr>`;
                    });
                } else {
                    throw new Error('Ranking vazio.');
                }
            } catch (error) {
                loader.innerHTML = '<p class="text-muted py-3">Ranking ainda não disponível.</p>';
            }
        }

        // Substitua a função buildTournamentCard existente por esta VERSÃO ATUALIZADA
        function buildTournamentCard(camp) {
            const dataInicio = new Date(camp.data_inicio_prevista).toLocaleDateString('pt-BR');
            const valor = camp.valor_inscricao > 0 ? `R$ ${parseFloat(camp.valor_inscricao).toFixed(2).replace('.', ',')}` : 'Grátis';

            // --- LÓGICA DOS BOTÕES (Nova) ---
            let buttonsHtml = '';

            // Botão de Inscrição/Detalhes (Se inscrições estiverem abertas)
            if (camp.status === 'inscricoes_abertas') {
                // Se estiver na home do estado (currentSlug existe), usa o link do estado, senão o global
                const baseLink = (typeof currentSlug !== 'undefined' && currentSlug) ? `/${currentSlug}/campeonatos` : '/campeonatos';
                buttonsHtml += `<a href="${baseLink}" class="btn btn-primary fw-bold w-100 mb-2">Ver Detalhes / Inscrever</a>`;
            }

            // NOVO: Botão Ver Chaves (Aparece se o torneio NÃO estiver apenas "criado" ou "inscricoes_abertas")
            // Ou seja, aparece para 'checkin_aberto', 'em_andamento', 'finalizado'
            if (camp.status !== 'criado' && camp.status !== 'inscricoes_abertas') {
                // Usa o estilo outline-light para destacar menos que o botão de inscrição se ambos existirem
                const btnClass = (buttonsHtml !== '') ? 'btn-outline-light' : 'btn-primary';
                buttonsHtml += `<a href="/pages/chaves.php?id=${camp.id}" class="btn ${btnClass} fw-bold w-100"><i class="bi bi-diagram-3 me-2"></i> Ver Chaves</a>`;
            }

            // Se não tiver nenhum botão (ex: torneio recém-criado), põe um aviso
            if (buttonsHtml === '') {
                buttonsHtml = '<button class="btn btn-secondary w-100" disabled>Aguardando Início</button>';
            }
            // --------------------------------

            return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 card-camp-home text-white">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-primary">${camp.jogo}</span>
                        <span class="badge bg-dark border"><i class="bi bi-geo-alt"></i> ${camp.sigla_estado || ''}</span>
                    </div>
                    <h4 class="card-title font-impact mb-3 text-truncate">${camp.nome}</h4>
                    <ul class="list-unstyled small text-muted mb-4 flex-grow-1">
                            <li class="mb-2"><i class="bi bi-calendar-event me-2 text-primary"></i> Início: <strong>${dataInicio}</strong></li>
                            <li class="mb-2"><i class="bi bi-ticket-perforated me-2 text-primary"></i> Inscrição: <strong>${valor}</strong></li>
                            <li class="mb-2 text-capitalize"><i class="bi bi-info-circle me-2 text-primary"></i> Status: <strong>${camp.status.replace('_', ' ')}</strong></li>
                    </ul>
                    <div class="mt-auto">
                        ${buttonsHtml}
                    </div>
                </div>
            </div>
        </div>
    `;
        }
    </script>
</body>

</html>