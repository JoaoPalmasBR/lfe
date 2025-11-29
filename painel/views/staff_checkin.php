<?php
// Arquivo: /painel/views/staff_checkin.php
// View: Tela de Check-in para Staff/Gestores.
// Variáveis disponíveis: $currentUserFront
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in de Jogadores | Staff LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Ajuste para o container da câmera */
        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            background-color: #000;
        }

        /* Esconde o botão de "Stop Scanning" que a biblioteca gera automaticamente */
        #reader button {
            display: none;
        }
    </style>
</head>

<body class="bg-dark text-light">

    <nav class="navbar navbar-dark bg-black shadow-sm mb-4 border-bottom border-secondary">
        <div class="container">
            <span class="navbar-brand fw-bold">
                <i class="bi bi-shield-check me-2 text-warning"></i> Área da Staff
            </span>
            <div class="d-flex text-white align-items-center small">
                <span class="me-3 d-none d-sm-block">Op: <?php echo htmlspecialchars($currentUserFront['nome_completo']); ?></span>
                <a href="<?php echo BASE_URL; ?>/painel" class="btn btn-sm btn-outline-light">Voltar ao Painel</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">

                <div class="card bg-secondary text-white border-0 shadow-lg mb-4">
                    <div class="card-body text-center p-4">
                        <h4 class="fw-bold mb-3"><i class="bi bi-qr-code-scan me-2"></i> Validador de Entrada</h4>
                        <p class="text-light opacity-75 small mb-4">Aponte a câmera para o QR Code do jogador.</p>

                        <div id="reader" class="rounded overflow-hidden mb-3 border border-warning"></div>
                        <div id="camera-status" class="small text-warning mb-3">Iniciando câmera...</div>

                        <div id="checkin-result-success" class="alert alert-success d-flex align-items-center d-none" role="alert">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div class="text-start">
                                <strong class="d-block" id="success-title">Check-in Realizado!</strong>
                                <small id="success-msg">Jogador liberado.</small>
                            </div>
                        </div>

                        <div id="checkin-result-error" class="alert alert-danger d-flex align-items-center d-none" role="alert">
                            <i class="bi bi-x-circle-fill fs-4 me-3"></i>
                            <div class="text-start">
                                <strong class="d-block" id="error-title">Erro na Validação</strong>
                                <small id="error-msg">QR Code inválido.</small>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card bg-dark border-secondary text-white shadow-sm">
                    <div class="card-header border-secondary fw-bold">
                        <i class="bi bi-keyboard me-2"></i> Validação Manual
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" id="manual-hash-input" class="form-control bg-dark text-white border-secondary" placeholder="Digite o hash do código...">
                            <button class="btn btn-warning fw-bold" type="button" id="btn-manual-checkin">VALIDAR</button>
                        </div>
                        <small class="text-muted mt-2 d-block">Use apenas se o scanner falhar.</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>'
        };
    </script>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?php echo BASE_URL; ?>/painel/js/staff_checkin.js?v=<?php echo time(); ?>"></script>

</body>

</html>