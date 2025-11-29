<?php
// Define a cor e os links do menu baseado na função
$navColor = 'bg-dark'; // Padrão admin
$navLinks = [];

switch ($currentUserFront['funcao']) {
    case 'jogador':
        $navColor = 'bg-primary';
        // Links específicos do Jogador
        $navLinks[] = ['url' => BASE_URL . '/painel', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'];
        // Adicione outros links de jogador aqui (ex: Meus Jogos)
        break;
    case 'gestor_estadual':
    case 'staff':
        $navColor = 'bg-success';
        $navLinks[] = ['url' => BASE_URL . '/painel', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'];
        // Link para o Check-in
        $navLinks[] = ['url' => BASE_URL . '/painel/checkin', 'icon' => 'bi-qr-code-scan', 'label' => 'Check-in Staff'];
        break;
    case 'super_admin':
        $navColor = 'bg-dark';
        $navLinks[] = ['url' => BASE_URL . '/painel', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard Global'];
        break;
}
?>
<nav class="navbar navbar-expand-lg navbar-dark <?php echo $navColor; ?> mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>/painel">
            <i class="bi bi-controller me-2"></i>LFE Painel
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php foreach ($navLinks as $link): ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $link['url']; ?>">
                            <i class="bi <?php echo $link['icon']; ?> me-1"></i> <?php echo $link['label']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="text-white small d-none d-sm-inline">
                    Olá, <strong><?php echo htmlspecialchars($currentUserFront['nome_completo']); ?></strong>
                </span>

                <?php if ($currentUserFront['funcao'] === 'jogador'): ?>
                    <a href="<?php echo BASE_URL; ?>/painel/?view=jogador_perfil" class="btn btn-sm btn-light fw-bold text-primary d-flex align-items-center">
                        <i class="bi bi-person-circle me-1"></i>
                        <span class="d-none d-sm-inline">Meu Perfil</span>
                    </a>
                <?php endif; ?>

                <a href="<?php echo BASE_URL; ?>/login?logout=true" class="btn btn-sm btn-outline-light d-flex align-items-center">
                    <i class="bi bi-box-arrow-right me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>