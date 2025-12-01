<?php
// Arquivo: pages/chaves.php
// Objetivo: Visualização PÚBLICA (somente leitura) das chaves de um torneio.
// URL: /pages/chaves.php?id=X
// VERSÃO: CORRIGIDA FINAL (Sem acesso direto ao DB, header via JS)

// 1. CARREGAMENTO SEGURO DE DEPENDÊNCIAS BÁSICAS
require_once __DIR__ . '/../includes/functions.php';
// NOTA: REMOVIDO O ACESSO AO BANCO DE DADOS AQUI. O FRONT NÃO PODE ACESSAR O BANCO.

$campeonatoId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

if (!$campeonatoId) {
    die('<div class="container py-5 text-center text-white" style="background:#0a0a0a"><h1>Torneio não especificado.</h1></div>');
}
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-bracket/0.11.1/jquery.bracket.min.css">

    <style>
        :root {
            --bg-dark: #0a0a0a;
            --text-light: #e0e0e0;
            --primary-color: #0d6efd;
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

        /* Customização do Bracket para tema escuro */
        .jQBracket {
            font-family: 'Segoe UI', sans-serif;
        }

        .jQBracket .team {
            background-color: #1a1a1a;
            color: #fff;
            border: 1px solid #333;
        }

        .jQBracket .team.highlight {
            background-color: var(--primary-color);
            color: white;
        }

        .jQBracket .team.winner {
            background-color: #198754 !important;
            /* Verde sucesso */
        }

        .jQBracket .score {
            background-color: #000;
            color: #fff;
        }

        /* Muda o cursor para indicar clique */
        .jQBracket .match:hover .team {
            border-color: var(--primary-color);
            cursor: pointer;
        }

        /* Esconde ferramentas de edição */
        .jQBracket .tools {
            display: none !important;
        }
    </style>
</head>

<body class="d-flex flex-column h-100">

    <nav class="navbar navbar-dark bg-black py-3 border-bottom border-secondary mb-4">
        <div class="container">
            <a class="navbar-brand font-impact fs-4" href="javascript:history.back()"><i class="bi bi-arrow-left me-2"></i> Voltar</a>
            <span class="navbar-text font-impact fs-4 text-white text-truncate" id="header-camp-name">Carregando torneio...</span>
        </div>
    </nav>

    <main class="container flex-shrink-0 mb-5">
        <div class="text-center mb-4">
            <span class="badge bg-primary mb-2 font-impact fs-6" id="header-camp-game">...</span>
            <h1 class="font-impact">Chaveamento</h1>
            <p class="text-muted small mb-4">Clique em uma partida definida para ver a vitrine do confronto.</p>

            <div class="card bg-dark border-secondary shadow">
                <div class="card-body p-4 overflow-auto text-center">
                    <div id="bracket-container">
                        <div class="spinner-border text-primary" role="status"></div> Carregando chaveamento...
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>'
        };
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-bracket/0.11.1/jquery.bracket.min.js"></script>

    <script>
        const campeonatoId = <?php echo $campeonatoId; ?>;

        $(document).ready(function() {
            carregarDadosEGerarBracket();
        });

        async function carregarDadosEGerarBracket() {
            try {
                // Usa a NOVA API pública dedicada que retorna meta + dados
                const response = await fetch(`${window.LFE_CONFIG.API_URL}/public/chaves_dados.php?campeonato_id=${campeonatoId}`);
                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // 1. Atualiza o cabeçalho da página com os metadados
                    $('#header-camp-name').text(result.data.meta.nome);
                    $('#header-camp-game').text(result.data.meta.jogo);
                    document.title = `Chaves: ${result.data.meta.nome} | LFE`;

                    // 2. Renderiza o bracket
                    $('#bracket-container').empty(); // Limpa loader

                    $('#bracket-container').bracket({
                        init: result.data.bracketData, // Usa a parte específica dos dados visuais
                        // Handler de clique na partida
                        onMatchClick: function(data) {
                            if (data && data.matchId) {
                                window.location.href = `/pages/partida.php?id=${data.matchId}`;
                            }
                        },
                        decorator: {
                            edit: function() {},
                            render: renderMatchView
                        }
                    });
                } else {
                    // Mostra mensagem de erro da API (ex: chaves não prontas)
                    $('#bracket-container').html(`<p class="text-danger fw-bold">${result.message || 'Não foi possível carregar as chaves.'}</p>`);
                    $('#header-camp-name').text('Torneio Indisponível');
                }
            } catch (error) {
                console.error("Erro:", error);
                $('#bracket-container').html('<p class="text-danger fw-bold">Erro de conexão ao buscar dados das chaves.</p>');
            }
        }

        // Função auxiliar para customizar a exibição dos nomes/placar
        function renderMatchView(container, data, score, state) {
            switch (state) {
                case "empty-bye":
                    container.append('<small class="text-muted">Bye</small>');
                    return;
                case "empty-tbd":
                    container.append('<small class="text-muted">A Definir</small>');
                    return;
                case "entry-no-score":
                case "entry-default-win":
                case "entry-complete":
                    if (data) {
                        container.append('<span class="nick fw-semibold">' + data + '</span>');
                        if (score !== null && score !== undefined) {
                            container.append('<span class="score badge bg-dark border ms-2">' + score + '</span>');
                        }
                    } else {
                        container.append('<small class="text-muted">A Definir</small>');
                    }
                    return;
            }
        }
    </script>
</body>

</html>