<?php
// Arquivo: /painel/views/staff_checkin.php
// View: Tela operacional para Staff/Gestor realizar check-in (Mobile First).
// Variáveis disponíveis: $currentUserFront, $userTokenFront

// Autorização extra: apenas gestor e staff podem ver isso
if (!in_array($currentUserFront['funcao'], ['gestor_estadual', 'staff'])) {
    die("Acesso negado. Apenas Staff.");
}

$msgSucesso = null;
$msgErro = null;
$dadosCheckin = null;

// =====================================================================
// PROCESSAR O BIP/ENVIO DO QR CODE (POST)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_hash'])) {
    $qrHash = trim($_POST['qr_hash']);

    if (!empty($qrHash)) {
        // Chama a API de Check-in
        $apiResult = callAPI('POST', '/staff/checkin.php', ['qr_hash' => $qrHash], $userTokenFront);

        if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
            // SUCESSO! Mostra os dados do jogador na tela.
            $msgSucesso = $apiResult['data']['message'];
            $dadosCheckin = $apiResult['data']; // Contém nome do jogador, campeonato, etc.
        } else {
            // ERRO (Ex: já bipado, hash inválido, outro estado)
            $msgErro = isset($apiResult['message']) ? $apiResult['message'] : "Erro ao validar QR Code.";
        }
    } else {
        $msgErro = "Por favor, leia ou digite o código do voucher.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portaria | LFE Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f0f2f5;
        }

        .card-checkin {
            max-width: 500px;
            margin: 20px auto;
        }

        .success-box {
            border-left: 5px solid #198754;
            background-color: #d1e7dd;
        }

        /* Faz o input parecer um alvo grande para scanners */
        .input-qr {
            font-family: monospace;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark shadow-sm mb-3">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/painel">
                <i class="bi bi-chevron-left me-2"></i> Voltar
            </a>
            <span class="navbar-text text-white small">Operação de Portaria</span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow border-0 card-checkin">
            <div class="card-body p-4">

                <h4 class="fw-bold text-center mb-4"><i class="bi bi-qr-code-scan text-primary"></i> Validação de Entrada</h4>

                <?php if ($msgErro): ?>
                    <div class="alert alert-danger text-center shadow-sm mb-4">
                        <i class="bi bi-x-circle-fill display-4 d-block mb-2"></i>
                        <strong>Acesso Negado:</strong> <br> <?php echo htmlspecialchars($msgErro); ?>
                    </div>
                    <hr>
                <?php endif; ?>

                <?php if ($msgSucesso && $dadosCheckin): ?>
                    <div class="p-4 mb-4 text-center success-box rounded shadow-sm">
                        <i class="bi bi-check-circle-fill text-success display-1"></i>
                        <h2 class="fw-bold text-success mt-3">ACESSO LIBERADO!</h2>
                        <h4 class="fw-bold mt-3"><?php echo htmlspecialchars($dadosCheckin['jogador']['nome']); ?></h4>
                        <p class="mb-1 text-muted">@<?php echo htmlspecialchars($dadosCheckin['jogador']['nickname']); ?></p>
                        <hr>
                        <p class="small fw-bold text-primary mb-0">
                            <i class="bi bi-trophy-fill"></i> <?php echo htmlspecialchars($dadosCheckin['campeonato']); ?>
                        </p>
                        <p class="small text-muted"><?php echo formatarDataHora($dadosCheckin['horario']); ?></p>
                    </div>
                    <div class="d-grid mb-4">
                        <a href="<?php echo BASE_URL; ?>/painel/checkin" class="btn btn-outline-primary fw-bold">
                            Ler Próximo Jogador
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (!$msgSucesso): ?>
                    <form method="POST" autocomplete="off">
                        <div class="mb-3">
                            <label for="qr_hash" class="form-label fw-bold small text-muted">Bipe ou digite o código do voucher:</label>
                            <input type="text" class="form-control form-control-lg input-qr text-center" id="qr_hash" name="qr_hash" placeholder="Aguardando leitura..." required autofocus>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                VALIDAR ENTRADA <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                    <p class="text-center small text-muted mt-3">Mantenha esta tela aberta no celular da portaria.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</body>

</html>