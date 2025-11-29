<?php
// Arquivo: /painel/views/jogador_perfil.php
// View: Tela de edição de perfil do jogador.
// Variáveis disponíveis: $currentUserFront (dados básicos da sessão)
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-person-badge-fill me-2"></i> Meu Perfil</h1>
</div>

<div id="profile-loader" class="text-center py-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2 text-muted">Carregando suas informações...</p>
</div>

<div id="profile-feedback" class="alert d-none alert-dismissible fade show" role="alert">
    <span id="profile-feedback-msg"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div id="profile-content" class="row d-none">

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="position-relative d-inline-block mb-3">
                    <img src="https://via.placeholder.com/150" id="avatar-preview" class="rounded-circle img-thumbnail shadow-sm" alt="Avatar" style="width: 150px; height: 150px; object-fit: cover;">

                    <label for="avatar-upload-input" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle" style="cursor: pointer;" title="Alterar Foto">
                        <i class="bi bi-camera-fill"></i>
                        <input type="file" id="avatar-upload-input" accept="image/png, image/jpeg" style="display: none;">
                    </label>
                </div>

                <h3 class="fw-bold mb-0" id="resumo-nickname">Carregando...</h3>
                <p class="text-muted small mb-2" id="resumo-nome-completo">...</p>

                <div class="d-inline-block bg-warning text-dark px-3 py-1 rounded-pill fw-bold">
                    <i class="bi bi-star-fill"></i> <span id="resumo-pontos">0</span> pts
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white p-0">
                <ul class="nav nav-tabs card-header-tabs nav-fill m-0" id="perfilTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-3" id="gamer-tab" data-bs-toggle="tab" data-bs-target="#gamer-tab-pane" type="button" role="tab">
                            <i class="bi bi-controller me-2"></i> Perfil Gamer (Público)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-3" id="pessoal-tab" data-bs-toggle="tab" data-bs-target="#pessoal-tab-pane" type="button" role="tab">
                            <i class="bi bi-person-vcard me-2"></i> Dados Pessoais (Privado)
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <form id="form-perfil">
                    <div class="tab-content" id="perfilTabContent">

                        <div class="tab-pane fade show active" id="gamer-tab-pane" role="tabpanel">
                            <div class="mb-4">
                                <label for="input-nickname" class="form-label fw-bold">Nickname (Apelido no Jogo)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="text" class="form-control" id="input-nickname" name="nickname" maxlength="50" required>
                                </div>
                                <div class="form-text">Este é o nome que aparecerá nas chaves e no ranking.</div>
                            </div>

                            <div class="mb-4">
                                <label for="input-biografia" class="form-label fw-bold">Biografia / Sobre Mim</label>
                                <textarea class="form-control" id="input-biografia" name="biografia" rows="3" maxlength="500" placeholder="Conte um pouco sobre sua experiência nos e-sports..."></textarea>
                            </div>

                            <h5 class="fw-bold mb-3"><i class="bi bi-share-fill me-2"></i> Redes Sociais <small class="text-muted fw-normal">(Opcional)</small></h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small"><i class="bi bi-discord text-primary"></i> Discord (Usuário#0000)</label>
                                    <input type="text" class="form-control form-control-sm" id="input-discord" name="link_discord" placeholder="Ex: Player#1234">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small"><i class="bi bi-twitch text-purple"></i> Twitch (URL)</label>
                                    <input type="url" class="form-control form-control-sm" id="input-twitch" name="link_twitch" placeholder="https://twitch.tv/seu_canal">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small"><i class="bi bi-instagram text-danger"></i> Instagram (URL)</label>
                                    <input type="url" class="form-control form-control-sm" id="input-instagram" name="link_instagram" placeholder="https://instagram.com/seu_perfil">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pessoal-tab-pane" role="tabpanel">
                            <div class="alert alert-info small mb-4">
                                <i class="bi bi-shield-lock-fill me-1"></i> Estes dados são usados apenas para processamento de pagamentos e validação de identidade. Eles não são exibidos publicamente.
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="input-nome-completo" class="form-label fw-bold">Nome Completo</label>
                                    <input type="text" class="form-control bg-light" id="input-nome-completo" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label for="input-email" class="form-label fw-bold">E-mail</label>
                                    <input type="email" class="form-control bg-light" id="input-email" readonly disabled>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="input-cpf" class="form-label fw-bold">CPF</label>
                                    <input type="text" class="form-control" id="input-cpf" name="cpf" placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-4">
                                    <label for="input-telefone" class="form-label fw-bold">Telefone/WhatsApp</label>
                                    <input type="tel" class="form-control" id="input-telefone" name="telefone" placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-4">
                                    <label for="input-nascimento" class="form-label fw-bold">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="input-data-nascimento" name="data_nascimento">
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3"><i class="bi bi-geo-alt-fill me-2"></i> Localização</h5>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="input-cidade" class="form-label fw-bold">Cidade</label>
                                    <input type="text" class="form-control" id="input-cidade" name="cidade">
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="mt-4 text-end border-top pt-3">
                        <button type="submit" class="btn btn-primary fw-bold px-4" id="btn-salvar-perfil">
                            <i class="bi bi-save2-fill me-2"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>/painel/js/jogador_perfil.js?v=<?php echo time(); ?>"></script>