<!DOCTYPE html>
<html lang="pt-br" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel LFE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .main-content {
            min-height: 80vh;
        }
    </style>
    <script>
        window.LFE_CONFIG = {
            API_URL: '<?php echo API_URL; ?>',
            BASE_URL: '<?php echo BASE_URL; ?>'
        };
    </script>
</head>

<body class="d-flex flex-column h-100">