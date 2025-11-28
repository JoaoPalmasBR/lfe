// Arquivo: /painel/js/jogador_partidas.js
// Objetivo: Buscar e renderizar dados do painel do jogador (Partidas e Inscrições) via AJAX.
// VERSÃO: CORRIGIDA (Sintaxe JS)

document.addEventListener('DOMContentLoaded', function() {
    if (!window.LFE_CONFIG || !window.LFE_CONFIG.API_URL) {
        console.error('[ERRO CONFIG] API_URL não definida.'); return;
    }
    // Inicia o carregamento das duas seções em paralelo
    carregarMinhasPartidas();
    carregarMinhasInscricoes();
});

// --- FUNÇÕES AUXILIARES ---
function getAuthToken() {
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

function formatarDataJS(dataString) {
    if(!dataString) return 'Data não informada';
    const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    // Tenta criar a data. Se falhar, retorna a string original.
    try {
        return new Date(dataString).toLocaleDateString('pt-BR', options);
    } catch (e) {
        return dataString;
    }
}

// Função genérica para controlar visibilidade de loaders/containers
function toggleSectionView(sectionPrefix, state) {
    // state pode ser: 'loading', 'empty', 'error', 'content'
    const loader = document.getElementById(`${sectionPrefix}-loader`);
    const vazia = document.getElementById(`${sectionPrefix}-vazia`);
    const erro = document.getElementById(`${sectionPrefix}-erro`);
    const container = document.getElementById(`${sectionPrefix}-container`);

    // 1. Esconde tudo primeiro
    if(loader) loader.classList.add('d-none');
    if(vazia) vazia.classList.add('d-none');
    if(erro) erro.classList.add('d-none');
    if(container) { 
        container.classList.add('d-none'); 
        container.classList.remove('d-flex'); // Remove d-flex por precaução
    }

    // 2. Mostra apenas o elemento desejado
    switch(state) {
        case 'loading': if(loader) loader.classList.remove('d-none'); break;
        case 'empty': if(vazia) vazia.classList.remove('d-none'); break;
        case 'error': if(erro) erro.classList.remove('d-none'); break;
        case 'content': 
            if(container) { 
                container.classList.remove('d-none'); 
                // Adiciona d-flex APENAS se for a seção de jogos, pois a de inscrições usa grid bootstrap
                if(sectionPrefix === 'meus-jogos') container.classList.add('d-flex');
            } 
            break;
    }
}

// ==================================================================
// SEÇÃO 1: MEUS PRÓXIMOS JOGOS
// ==================================================================
function carregarMinhasPartidas() {
    toggleSectionView('meus-jogos', 'loading');
    const token = getAuthToken();

    fetch(`${window.LFE_CONFIG.API_URL}/jogador/minhas_partidas.php`, {
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.status === 'success') {
            if (Array.isArray(result.data) && result.data.length > 0) {
                renderizarMeusJogos(result.data);
                toggleSectionView('meus-jogos', 'content');
            } else {
                toggleSectionView('meus-jogos', 'empty');
            }
        } else { throw new Error(result.message); }
    })
    .catch(e => {
        console.error('[ERRO JOGOS]', e);
        const erroEl = document.getElementById('meus-jogos-erro');
        if(erroEl) erroEl.innerText = 'Não foi possível carregar seus jogos.';
        toggleSectionView('meus-jogos', 'error');
    });
}

function renderizarMeusJogos(lista) {
    const container = document.getElementById('meus-jogos-container');
    container.innerHTML = '';
    lista.forEach(p => {
        let statusBadge = '<span class="badge bg-secondary">Agendada</span>';
        if(p.status === 'em_andamento') statusBadge = '<span class="badge bg-danger animate-pulse">Ao Vivo / Em Jogo</span>';
        const oponenteNick = p.oponente_nick || 'A Definir';

        const html = `
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 border-primary shadow-sm">
                <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center small fw-bold">
                    <span class="text-truncate me-2"><i class="bi bi-trophy-fill me-1"></i> ${p.nome_campeonato}</span>
                    ${statusBadge}
                </div>
                <div class="card-body text-center py-4 d-flex flex-column justify-content-center">
                    <p class="text-muted text-uppercase small ls-1 mb-3 fw-bold">Rodada ${p.rodada}</p>
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="flex-fill"><span class="badge bg-success mb-2">VOCÊ</span><h5 class="fw-bold text-dark mb-0">(Seu lado)</h5></div>
                        <div class="h4 text-muted mx-3 opacity-50">VS</div>
                        <div class="flex-fill"><span class="badge bg-danger mb-2">OPONENTE</span><h5 class="fw-bold text-dark mb-0 text-truncate">@${oponenteNick}</h5></div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 text-center pb-3"><small class="text-muted">Aguarde instruções do organizador.</small></div>
            </div>
        </div>`;
        container.innerHTML += html;
    });
}

// ==================================================================
// SEÇÃO 2: MINHAS INSCRIÇÕES
// ==================================================================
function carregarMinhasInscricoes() {
    toggleSectionView('inscricoes', 'loading');
    const token = getAuthToken();

    fetch(`${window.LFE_CONFIG.API_URL}/user/minhas_inscricoes.php`, {
        method: 'GET',
        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.status === 'success') {
            if (Array.isArray(result.data) && result.data.length > 0) {
                renderizarInscricoes(result.data);
                toggleSectionView('inscricoes', 'content');
            } else {
                toggleSectionView('inscricoes', 'empty');
            }
        } else { throw new Error(result.message); }
    })
    .catch(e => {
        console.error('[ERRO INSCRICOES]', e);
        const erroEl = document.getElementById('inscricoes-erro');
        if(erroEl) erroEl.innerText = 'Erro ao carregar inscrições.';
        toggleSectionView('inscricoes', 'error');
    });
}

function renderizarInscricoes(lista) {
    const container = document.getElementById('inscricoes-container');
    container.innerHTML = '';

    lista.forEach(inscricao => {
        // Lógica visual dos status
        let statusBadge = 'bg-secondary'; let statusTexto = 'Desconhecido';
        if (inscricao.status === 'aguardando_pagamento') { statusBadge = 'bg-warning text-dark'; statusTexto = 'Aguardando Pagamento'; }
        else if (inscricao.status === 'confirmado') { statusBadge = 'bg-success'; statusTexto = 'Confirmado'; }
        else if (inscricao.status === 'lista_espera') { statusBadge = 'bg-info text-dark'; statusTexto = 'Lista de Espera'; }
        else if (inscricao.status === 'checkin_realizado') { statusBadge = 'bg-primary'; statusTexto = 'Check-in Realizado'; }

        // Lógica dos botões de ação
        let actionButtons = '';
        // --- CORREÇÃO AQUI: Usando 'else if' corretamente ---
        if (inscricao.status === 'aguardando_pagamento') {
            actionButtons = `
                <button class="btn btn-warning fw-bold btn-pagar w-100" data-inscricao-id="${inscricao.inscricao_id}" onclick="iniciarPagamentoReal(this)">
                    <span class="texto-btn"><i class="bi bi-cash-coin me-2"></i> PAGAR AGORA (Pix)</span>
                    <span class="spinner-btn d-none"><span class="spinner-border spinner-border-sm me-2"></span>Gerando Pix...</span>
                </button>
                <small class="text-center text-muted mt-2 d-block">Pagamento instantâneo via Pix.</small>
            `;
        } else if (inscricao.status === 'confirmado') {
            actionButtons = `
                <button class="btn btn-success fw-bold w-100 py-2" data-hash="${inscricao.hash_qr_code}" onclick="abrirModalQR(this)">
                    <i class="bi bi-qr-code me-2"></i> VISUALIZAR VOUCHER / QR CODE
                </button>
                <small class="text-center text-success mt-2 fw-bold d-block">Tudo pronto para o evento!</small>
            `;
        }
        // ----------------------------------------------------

        const html = `
        <div class="col">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="badge ${statusBadge}">${statusTexto}</span>
                    <small class="text-muted">${inscricao.estado_sigla}</small>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">${inscricao.campeonato_nome}</h5>
                    <p class="card-text small text-muted">
                        <i class="bi bi-calendar-event me-1"></i> Data: ${formatarDataJS(inscricao.data_inicio_prevista)}
                    </p>
                    <div class="mt-4">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        </div>`;
        container.innerHTML += html;
    });
}