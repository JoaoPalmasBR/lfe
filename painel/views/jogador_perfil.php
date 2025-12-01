<?php
// Arquivo: /painel/views/jogador_perfil.php
// View: Tela de edição de perfil do jogador.
// VERSÃO: COM UPLOAD DE FOTO PRÉ-JOGO (VITRINE)
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-person-badge-fill me-2"></i> Meu Perfil</h1>
</div>

<div id="profile-loader" class="text-center py-5">
    <div class="spinner-border text-primary" role="status"></div>
    <p class="mt-2 text-muted">Carregando suas informações...</p>
</div>

<div id="profile-feedback" class="alert d-none alert-dismissible fade show shadow-sm" role="alert">
    <span id="profile-feedback-msg"></span>
</div>

<div id="profile-content" class="row d-none">

    <div class="col-md-4 mb-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <div class="position-relative d-inline-block mb-3">
                    <img src="" id="avatar-preview" class="rounded-circle img-thumbnail shadow-sm bg-light" alt="Avatar" style="width: 150px; height: 150px; object-fit: cover;">

                    <label for="avatar-upload-input" class="position-absolute bottom-0 end-0 btn btn-sm btn-primary rounded-circle shadow" style="cursor: pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;" title="Alterar Foto de Perfil">
                        <i class="bi bi-camera-fill fs-5"></i>
                        <input type="file" id="avatar-upload-input" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;">
                    </label>
                </div>

                <h3 class="fw-bold mb-0 text-truncate" id="resumo-nickname">Carregando...</h3>
                <p class="text-muted small mb-2 text-truncate" id="resumo-nome-completo">...</p>

                <div class="d-inline-block bg-warning bg-opacity-25 text-dark px-3 py-1 rounded-pill fw-bold">
                    <i class="bi bi-star-fill text-warning"></i> <span id="resumo-pontos">0</span> pts
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
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
                                    <span class="input-group-text bg-light"><i class="bi bi-at"></i></span>
                                    <input type="text" class="form-control" id="input-nickname" name="nickname" maxlength="50" required placeholder="Como você quer ser chamado?">
                                </div>
                                <div class="form-text">Este é o nome principal que aparecerá nas chaves e ranking.</div>
                            </div>

                            <div class="mb-4">
                                <label for="input-biografia" class="form-label fw-bold">Biografia / Sobre Mim</label>
                                <textarea class="form-control" id="input-biografia" name="biografia" rows="3" maxlength="500" placeholder="Conte um pouco sobre sua experiência, jogos favoritos, etc..."></textarea>
                            </div>

                            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-share-fill me-2"></i> Redes Sociais <small class="fw-normal opacity-75">(Opcional)</small></h5>

                            <div class="row g-3 mb-5">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-discord text-primary"></i> Discord</label>
                                    <input type="text" class="form-control form-control-sm" id="input-discord" name="link_discord" placeholder="Usuário#0000">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-twitch text-purple" style="color: #6f42c1;"></i> Twitch</label>
                                    <input type="url" class="form-control form-control-sm" id="input-twitch" name="link_twitch" placeholder="https://twitch.tv/...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold"><i class="bi bi-instagram text-danger"></i> Instagram</label>
                                    <input type="url" class="form-control form-control-sm" id="input-instagram" name="link_instagram" placeholder="https://instagram.com/...">
                                </div>
                            </div>

                            <div class="border-top pt-4">
                                <h5 class="fw-bold mb-3 text-secondary">
                                    <i class="bi bi-image-fill me-2"></i> Foto de Vitrine (Estilo "X1")
                                </h5>
                                <div class="card border-dashed bg-light shadow-sm">
                                    <div class="card-body text-center p-4">

                                        <img src="https://placehold.co/600x300/e9ecef/6c757d?text=Sem+Foto+Vitrine"
                                            id="pregame-preview"
                                            class="img-fluid rounded mb-3 shadow-sm border"
                                            alt="Preview Vitrine"
                                            style="max-height: 250px; width: 100%; object-fit: cover; background-color: #ddd;">

                                        <p class="small text-muted mb-3">
                                            Esta foto será usada em telas de "versus" antes das partidas.
                                            <br><strong>Recomendado:</strong> Foto horizontal (paisagem) de alta qualidade. Máx: 8MB.
                                        </p>

                                        <label for="pregame-upload-input" class="btn btn-outline-primary fw-bold">
                                            <i class="bi bi-cloud-upload-fill me-2"></i> Alterar Foto de Vitrine
                                            <input type="file" id="pregame-upload-input" accept="image/png, image/jpeg, image/jpg, image/webp" style="display: none;">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pessoal-tab-pane" role="tabpanel">
                            <div class="alert alert-info small mb-4 border-0 bg-info bg-opacity-10 text-info fw-bold">
                                <i class="bi bi-shield-lock-fill me-2"></i> Dados privados. Usados apenas para validação e pagamentos.
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nome Completo</label>
                                    <input type="text" class="form-control bg-light" id="input-nome-completo" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">E-mail</label>
                                    <input type="email" class="form-control bg-light" id="input-email" readonly disabled>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="input-cpf" class="form-label fw-bold">CPF</label>
                                    <input type="text" class="form-control font-monospace" id="input-cpf" name="cpf" placeholder="000.000.000-00">
                                </div>
                                <div class="col-md-4">
                                    <label for="input-telefone" class="form-label fw-bold">Telefone / WhatsApp</label>
                                    <input type="tel" class="form-control font-monospace" id="input-telefone" name="telefone" placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-4">
                                    <label for="input-nascimento" class="form-label fw-bold">Data de Nascimento</label>
                                    <input type="date" class="form-control" id="input-data-nascimento" name="data_nascimento">
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-geo-alt-fill me-2"></i> Localização</h5>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label for="input-cidade" class="form-label fw-bold">Cidade</label>
                                    <input type="text" class="form-control" id="input-cidade" name="cidade" placeholder="Sua cidade atual">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 text-end border-top pt-4">
                        <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" id="btn-salvar-perfil">
                            <i class="bi bi-save2-fill me-2"></i> Salvar Alterações de Texto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilo adicional para o card tracejado de upload */
    .border-dashed {
        border-style: dashed !important;
        border-width: 2px !important;
    }
</style>

<script src="<?php echo BASE_URL; ?>/painel/js/jogador_perfil.js?v=<?php echo time(); ?>"></script>