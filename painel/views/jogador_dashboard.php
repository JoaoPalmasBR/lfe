<?php
// Arquivo: /painel/views/jogador_dashboard.php
// View: Dashboard inicial do Jogador.
// Variáveis: $currentUserFront, $dadosView['inscricoes']
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Meu Painel</h1>
</div>
<div class="container">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold py-3 text-primary">
            <i class="bi bi-controller me-2"></i> Meus Próximos Jogos
        </div>
        <div class="card-body bg-light p-4">

            <div id="meus-jogos-loader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted small">Buscando sua agenda...</p>
            </div>

            <div id="meus-jogos-vazia" class="alert alert-success text-center d-none">
                <i class="bi bi-check-circle fs-4 d-block mb-2"></i>
                Você não tem jogos pendentes no momento!
                <br><small>Inscreva-se em novos campeonatos ou aguarde a próxima rodada.</small>
            </div>

            <div id="meus-jogos-erro" class="alert alert-danger text-center d-none"></div>

            <div id="meus-jogos-container" class="row g-3 d-none">
            </div>

        </div>
    </div>

    <h2 class="fw-bold mb-4">Minhas Inscrições</h2>

    <div id="inscricoes-loader" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2 text-muted small">Buscando seu histórico...</p>
    </div>

    <div id="inscricoes-vazia" class="alert alert-info text-center d-none">
        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
        Você ainda não está inscrito em nenhum campeonato.
        <br>
        <a href="<?php echo BASE_URL; ?>/campeonatos" class="alert-link">Ver torneios disponíveis</a>.
    </div>

    <div id="inscricoes-erro" class="alert alert-danger text-center d-none"></div>

    <div id="inscricoes-container" class="row row-cols-1 row-cols-md-2 g-4 mb-5 d-none">
    </div>

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

<script>
    window.LFE_CONFIG = {
        API_URL: '<?php echo API_URL; ?>'
    };
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo BASE_URL; ?>/painel/js/jogador_partidas.js?v=<?php echo time(); ?>"></script>

<script>
    // Função auxiliar para pegar o token do cookie (usada pelo script inline)
    function getAuthTokenInline() {
        const name = "lfe_token=";
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1);
            if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
        }
        return "";
    }

    // --- LÓGICA DE PAGAMENTO (ABACATEPAY) ---
    function iniciarPagamentoReal(btnElement) {
        const inscricaoId = btnElement.getAttribute('data-inscricao-id');
        const token = getAuthTokenInline();

        if (!token) {
            alert("Erro de autenticação. Faça login novamente.");
            window.location.href = '/login';
            return;
        }

        // Feedback visual
        btnElement.disabled = true;
        btnElement.querySelector('.texto-btn').classList.add('d-none');
        btnElement.querySelector('.spinner-btn').classList.remove('d-none');

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
                    // Sucesso: Redireciona para o gateway
                    window.location.href = result.body.data.payment_url;
                } else {
                    throw new Error(result.body.message || 'Erro ao gerar pagamento.');
                }
            })
            .catch(error => {
                alert("Não foi possível iniciar o pagamento:\n" + error.message);
                // Restaura o botão
                btnElement.disabled = false;
                btnElement.querySelector('.texto-btn').classList.remove('d-none');
                btnElement.querySelector('.spinner-btn').classList.add('d-none');
            });
    }

    // --- LÓGICA DE QR CODE ---
    var qrCodeGenerator = null;
    // Inicializa o modal do Bootstrap com verificação de segurança
    var modalElement = document.getElementById('modalQR');
    var modalBootstrap = modalElement ? new bootstrap.Modal(modalElement) : null;
    var containerQR = document.getElementById("qrcode-container");
    var textoHash = document.getElementById("hash-texto-modal");

    function abrirModalQR(botaoClicado) {
        if (!modalBootstrap || !containerQR) return;

        var hash = botaoClicado.getAttribute('data-hash');
        containerQR.innerHTML = ''; // Limpa anterior
        textoHash.textContent = hash;

        // Gera o novo QR Code
        qrCodeGenerator = new QRCode(containerQR, {
            text: hash,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        modalBootstrap.show();
    }
</script>
</body>

</html>