<?php
// Arquivo: /painel/views/jogador_dashboard.php
// View: Tela inicial do jogador logado.
// Variáveis disponíveis: $currentUserFront, $dadosView['inscricoes']
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel do Jogador | LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">LFE Painel</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3 small">Olá, <?php echo htmlspecialchars($currentUserFront['nome_completo']); ?></span>
                <a href="<?php echo BASE_URL; ?>/login" class="btn btn-sm btn-outline-light">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2 class="fw-bold mb-4">Meus Campeonatos</h2>

        <?php if (empty($dadosView['inscricoes'])): ?>
            <div class="alert alert-info" role="alert">
                Você ainda não está inscrito em nenhum campeonato.
                <a href="<?php echo BASE_URL; ?>/campeonatos" class="alert-link">Ver torneios disponíveis</a>.
            </div>
        <?php else: ?>

            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php foreach ($dadosView['inscricoes'] as $inscricao): ?>
                    <?php
                    // Lógica visual dos status
                    $statusBadge = 'bg-secondary';
                    $statusTexto = 'Desconhecido';
                    if ($inscricao['status'] === 'aguardando_pagamento') {
                        $statusBadge = 'bg-warning text-dark';
                        $statusTexto = 'Aguardando Pagamento';
                    } elseif ($inscricao['status'] === 'confirmado') {
                        $statusBadge = 'bg-success';
                        $statusTexto = 'Confirmado';
                    } elseif ($inscricao['status'] === 'lista_espera') {
                        $statusBadge = 'bg-info text-dark';
                        $statusTexto = 'Lista de Espera';
                    } elseif ($inscricao['status'] === 'checkin_realizado') {
                        $statusBadge = 'bg-primary';
                        $statusTexto = 'Check-in Realizado';
                    }
                    ?>
                    <div class="col">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <span class="badge <?php echo $statusBadge; ?>"><?php echo $statusTexto; ?></span>
                                <small class="text-muted"><?php echo $inscricao['estado_sigla']; ?></small>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold mb-3"><?php echo htmlspecialchars($inscricao['campeonato_nome']); ?></h5>
                                <p class="card-text small text-muted">
                                    <i class="bi bi-calendar-event me-1"></i> Data: <?php echo formatarDataHora($inscricao['data_inicio_prevista']); ?>
                                </p>

                                <div class="mt-4 d-grid">
                                    <?php if ($inscricao['status'] === 'aguardando_pagamento'): ?>
                                        <button class="btn btn-warning fw-bold btn-pagar"
                                            data-inscricao-id="<?php echo $inscricao['inscricao_id']; ?>"
                                            onclick="iniciarPagamentoReal(this)">
                                            <span class="texto-btn"><i class="bi bi-cash-coin me-2"></i> PAGAR AGORA (Pix)</span>
                                            <span class="spinner-btn d-none"><span class="spinner-border spinner-border-sm me-2"></span>Gerando Pix...</span>
                                        </button>
                                        <small class="text-center text-muted mt-2">Pagamento instantâneo via Pix.</small>

                                    <?php elseif ($inscricao['status'] === 'confirmado'): ?>
                                        <button class="btn btn-success fw-bold w-100 py-2"
                                            data-hash="<?php echo $inscricao['hash_qr_code']; ?>"
                                            onclick="abrirModalQR(this)">
                                            <i class="bi bi-qr-code me-2"></i> VISUALIZAR VOUCHER / QR CODE
                                        </button>
                                        <small class="text-center text-success mt-2 fw-bold">Tudo pronto para o evento!</small>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="modal fade" id="modalQR" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 justify-content-center pb-0">
                    <h5 class="modal-title fw-bold text-primary">Seu Voucher de Acesso</h5>
                </div>
                <div class="modal-body text-center pt-0">
                    <p class="text-muted small mb-4">Apresente este código na portaria do evento.</p>

                    <div id="qrcode-container" class="d-flex justify-content-center p-3 bg-white rounded border mb-3"></div>

                    <p class="small text-muted mb-0">Hash de segurança:</p>
                    <code class="small text-break" id="hash-texto-modal"></code>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function getAuthToken() {
            const name = "lfe_token=";
            const decodedCookie = decodeURIComponent(document.cookie);
            const ca = decodedCookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) === 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        // Função chamada ao clicar no botão pagar
        function iniciarPagamentoReal(btnElement) {
            const inscricaoId = btnElement.getAttribute('data-inscricao-id');
            const token = getAuthToken();

            if (!token) {
                alert("Erro de autenticação. Por favor, faça login novamente.");
                window.location.href = '/login';
                return;
            }

            // 1. Feedback visual (Carregando...)
            btnElement.disabled = true;
            btnElement.querySelector('.texto-btn').classList.add('d-none');
            btnElement.querySelector('.spinner-btn').classList.remove('d-none');

            // 2. Chama a API
            fetch('<?php echo API_URL; ?>/jogador/pagar.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({
                        inscricao_id: inscricaoId
                    })
                })
                .then(response => response.json().then(data => ({
                    status: response.status,
                    body: data
                })))
                .then(result => {
                    if (result.status >= 200 && result.status < 300) {
                        // SUCESSO! Redireciona para a Abacatepay
                        window.location.href = result.body.data.payment_url;
                    } else {
                        // ERRO DA API
                        throw new Error(result.body.message || 'Erro ao gerar pagamento.');
                    }
                })
                .catch(error => {
                    // Tratamento de erro
                    alert("Não foi possível iniciar o pagamento:\n" + error.message);
                    // Restaura o botão
                    btnElement.disabled = false;
                    btnElement.querySelector('.texto-btn').classList.remove('d-none');
                    btnElement.querySelector('.spinner-btn').classList.add('d-none');
                });
        }

        // Variável global para guardar a instância do gerador
        var qrCodeGenerator = null;
        var modalElement = document.getElementById('modalQR');
        var modalBootstrap = new bootstrap.Modal(modalElement);
        var containerQR = document.getElementById("qrcode-container");
        var textoHash = document.getElementById("hash-texto-modal");

        function abrirModalQR(botaoClicado) {
            // 1. Pega o hash que guardamos no atributo data-hash do botão
            var hash = botaoClicado.getAttribute('data-hash');

            // 2. Limpa o container (remove o QR code anterior se houver)
            containerQR.innerHTML = '';

            // 3. Mostra o texto do hash abaixo (opcional, para debug ou redundância)
            textoHash.textContent = hash;

            // 4. Gera o novo QR Code
            // Usamos a biblioteca QRCode que carregamos via CDN
            qrCodeGenerator = new QRCode(containerQR, {
                text: hash,
                width: 256,
                height: 256,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H // Alto nível de correção de erro
            });

            // 5. Abre o modal
            modalBootstrap.show();
        }
    </script>
</body>

</html>