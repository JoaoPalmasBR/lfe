// Arquivo: /painel/js/jogador_perfil.js
// Objetivo: Controlar visualização, edição de texto e uploads (Avatar e Vitrine).
// VERSÃO: COMPLETA COM UPLOAD PRÉ-JOGO

document.addEventListener('DOMContentLoaded', function() {
    carregarDadosPerfil();
    
    // Listener do formulário de texto
    document.getElementById('form-perfil').addEventListener('submit', function(e) {
        e.preventDefault();
        salvarPerfilTexto();
    });

    // --- LISTENER UPLOAD AVATAR (Foto Redonda) ---
    const avatarInput = document.getElementById('avatar-upload-input');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            handleFileUpload(this, uploadAvatar, 5); // Limite 5MB
        });
    }

    // --- NOVO: LISTENER UPLOAD VITRINE (Foto Retangular) ---
    const pregameInput = document.getElementById('pregame-upload-input');
    if (pregameInput) {
        pregameInput.addEventListener('change', function(e) {
            handleFileUpload(this, uploadPregamePhoto, 8); // Limite 8MB (conforme API)
        });
    }
});

// --- FUNÇÃO AUXILIAR PARA HANDLERS DE UPLOAD ---
function handleFileUpload(inputElement, uploadFunction, maxSizeMB) {
    if (inputElement.files && inputElement.files[0]) {
        const file = inputElement.files[0];
        // Validação de tamanho no front
        if (file.size > maxSizeMB * 1024 * 1024) {
            mostrarFeedbackPerfil('error', `A imagem é muito grande. O máximo é ${maxSizeMB}MB.`);
            inputElement.value = ''; // Limpa seleção
            return;
        }
        // Chama a função específica de upload
        uploadFunction(file);
    }
}

// --- FUNÇÕES DE CARREGAMENTO E PREENCHIMENTO ---

async function carregarDadosPerfil() {
    const loader = document.getElementById('profile-loader');
    const content = document.getElementById('profile-content');
    
    try {
        const token = getAuthToken(); // Global utils.js
        const response = await fetch(`${window.LFE_CONFIG.API_URL}/jogador/meu_perfil.php`, {
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
            popularFormulario(result.data);
            loader.classList.add('d-none');
            content.classList.remove('d-none');
        } else {
            throw new Error(result.message || 'Erro ao carregar dados.');
        }
    } catch (error) {
        console.error('Erro perfil:', error);
        loader.classList.add('d-none');
        mostrarFeedbackPerfil('error', 'Não foi possível carregar seus dados. Recarregue a página.');
    }
}

function popularFormulario(data) {
    // 1. Resumo Lateral
    document.getElementById('resumo-nickname').textContent = data.nickname || 'Sem Nickname';
    document.getElementById('resumo-nome-completo').textContent = data.nome_completo;
    document.getElementById('resumo-pontos').textContent = data.pontuacao_total || '0';
    
    // Avatar Principal (com placeholder dinâmico se vazio)
    const avatarUrl = data.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.nome_completo)}&background=random&color=fff&size=150`;
    document.getElementById('avatar-preview').src = avatarUrl;

    // 2. Aba Gamer
    document.getElementById('input-nickname').value = data.nickname || '';
    document.getElementById('input-biografia').value = data.biografia || '';
    document.getElementById('input-discord').value = data.link_discord || '';
    document.getElementById('input-twitch').value = data.link_twitch || '';
    document.getElementById('input-instagram').value = data.link_instagram || '';

    // --- NOVO: Foto de Vitrine (Pré-jogo) ---
    // Se não tiver foto, usa um placeholder retangular fixo
    const pregameUrl = data.foto_pre_jogo_url || 'https://placehold.co/600x300/e9ecef/6c757d?text=Sem+Foto+Vitrine';
    document.getElementById('pregame-preview').src = pregameUrl;

    // 3. Aba Pessoal
    document.getElementById('input-nome-completo').value = data.nome_completo || '';
    document.getElementById('input-email').value = data.email || '';
    document.getElementById('input-cpf').value = data.cpf || '';
    document.getElementById('input-telefone').value = data.telefone || '';
    document.getElementById('input-data-nascimento').value = data.data_nascimento || '';
    document.getElementById('input-cidade').value = data.cidade || '';
}

// --- FUNÇÕES DE SALVAR (TEXTO E IMAGENS) ---

async function salvarPerfilTexto() {
    const btnSalvar = document.getElementById('btn-salvar-perfil');
    const textoOriginal = btnSalvar.innerHTML;
    btnSalvar.disabled = true;
    btnSalvar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';
    mostrarFeedbackPerfil('info', 'Salvando alterações de texto...');

    try {
        const token = getAuthToken();
        const payload = {
            nickname: document.getElementById('input-nickname').value,
            biografia: document.getElementById('input-biografia').value,
            link_discord: document.getElementById('input-discord').value,
            link_twitch: document.getElementById('input-twitch').value,
            link_instagram: document.getElementById('input-instagram').value,
            cpf: document.getElementById('input-cpf').value,
            telefone: document.getElementById('input-telefone').value,
            data_nascimento: document.getElementById('input-data-nascimento').value,
            cidade: document.getElementById('input-cidade').value
        };

        const response = await fetch(`${window.LFE_CONFIG.API_URL}/jogador/editar_perfil.php`, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
            mostrarFeedbackPerfil('success', result.message);
            carregarDadosPerfil(); // Recarrega para atualizar resumo
            window.scrollTo(0, 0);
        } else {
            throw new Error(result.message || 'Erro ao salvar.');
        }
    } catch (error) {
        mostrarFeedbackPerfil('error', error.message);
        window.scrollTo(0, 0);
    } finally {
        btnSalvar.disabled = false;
        btnSalvar.innerHTML = textoOriginal;
    }
}

// Função genérica para upload (usada para avatar e vitrine)
async function genericUpload(file, apiEndpoint, previewImgId, inputId, successMsg) {
    const imgPreview = document.getElementById(previewImgId);
    const originalSrc = imgPreview.src;
    
    mostrarFeedbackPerfil('info', 'Enviando imagem... Aguarde.');
    imgPreview.style.opacity = '0.5';

    try {
        const token = getAuthToken();
        const formData = new FormData();
        // O nome do campo muda dependendo do endpoint, mas o PHP espera 'avatar_file' ou 'pregame_file'
        // Vamos simplificar e usar um nome genérico no JS, o PHP que se vire.
        // CORREÇÃO: O PHP espera nomes específicos. Vamos usar uma lógica simples.
        const fieldName = apiEndpoint.includes('pregame') ? 'pregame_file' : 'avatar_file';
        formData.append(fieldName, file);

        const response = await fetch(`${window.LFE_CONFIG.API_URL}/jogador/${apiEndpoint}`, {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + token }, // Content-Type é automático
            body: formData
        });
        const result = await response.json();

        if (response.ok && result.status === 'success') {
            mostrarFeedbackPerfil('success', successMsg);
            // Descobre qual chave da resposta tem a nova URL
            const newUrl = result.data.new_avatar_url || result.data.new_pregame_url;
            // Adiciona timestamp para quebrar cache do navegador
            imgPreview.src = newUrl + '?t=' + new Date().getTime();
        } else {
            throw new Error(result.message || 'Erro no upload.');
        }
    } catch (error) {
        console.error('Erro upload:', error);
        mostrarFeedbackPerfil('error', error.message);
        imgPreview.src = originalSrc;
    } finally {
        imgPreview.style.opacity = '1';
        document.getElementById(inputId).value = ''; // Limpa input
    }
}

// Wrappers específicos para cada tipo de upload
async function uploadAvatar(file) {
    await genericUpload(file, 'upload_avatar.php', 'avatar-preview', 'avatar-upload-input', 'Foto de perfil atualizada!');
}

// --- NOVO: WRAPPER PARA FOTO DE VITRINE ---
async function uploadPregamePhoto(file) {
    await genericUpload(file, 'upload_pregame.php', 'pregame-preview', 'pregame-upload-input', 'Foto de vitrine atualizada!');
}

// --- FUNÇÃO DE FEEDBACK VISUAL ---
let feedbackTimeout;
function mostrarFeedbackPerfil(tipo, mensagem) {
    const feedbackEl = document.getElementById('profile-feedback');
    const msgEl = document.getElementById('profile-feedback-msg');
    
    clearTimeout(feedbackTimeout); // Limpa timeout anterior se houver

    feedbackEl.classList.remove('alert-success', 'alert-danger', 'alert-info', 'd-none');
    
    let alertClass = 'alert-info';
    let icon = '<i class="bi bi-info-circle-fill me-2"></i>';
    
    if (tipo === 'success') {
        alertClass = 'alert-success';
        icon = '<i class="bi bi-check-circle-fill me-2"></i>';
    }
    if (tipo === 'error') {
        alertClass = 'alert-danger';
        icon = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
    }
    
    feedbackEl.classList.add(alertClass);
    msgEl.innerHTML = icon + mensagem;
    
    // Se não for mensagem de carregamento ('info'), esconde após 5s
    if (tipo !== 'info') {
        feedbackTimeout = setTimeout(() => {
            feedbackEl.classList.add('d-none');
        }, 5000);
    }
}