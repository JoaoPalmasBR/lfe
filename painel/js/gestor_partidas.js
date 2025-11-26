// Arquivo: /painel/js/gestor_partidas.js
// Objetivo: Controlar a lógica da aba de Partidas no painel do gestor.
// VERSÃO: CORREÇÃO DE VISIBILIDADE (FINAL V1)

console.log('--- [DEBUG] Script gestor_partidas.js carregado (Versão Final V1) ---');

// Inicializa assim que a página carregar
document.addEventListener('DOMContentLoaded', function() {
    if (!window.LFE_CONFIG || !window.LFE_CONFIG.CAMPEONATO_ID) {
        console.error('[ERRO CRÍTICO] Configurações LFE_CONFIG ausentes.');
        // Tenta mostrar erro visualmente se possível
        const container = document.getElementById('lista-partidas-container');
        if(container) {
            container.innerHTML = '<div class="alert alert-danger">Erro interno de configuração JS.</div>';
            container.classList.remove('d-none');
        }
        return;
    }
    carregarPartidas();
});

// Função auxiliar para pegar o token
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

// Função auxiliar para controlar visibilidade usando classes Bootstrap
function toggleVisibility(elementId, show, displayClass = 'd-block') {
    const el = document.getElementById(elementId);
    if (!el) return;
    
    if (show) {
        el.classList.remove('d-none');
        el.classList.add(displayClass);
    } else {
        el.classList.remove(displayClass);
        el.classList.add('d-none');
    }
}

/**
 * Busca a lista de partidas na API e renderiza na tela.
 */
function carregarPartidas() {
    // Reseta estado visual
    toggleVisibility('lista-partidas-loader', true);
    toggleVisibility('lista-partidas-container', false, 'd-flex');
    toggleVisibility('lista-partidas-vazia', false);

    const url = `${window.LFE_CONFIG.API_URL}/campeonatos/partidas.php?campeonato_id=${window.LFE_CONFIG.CAMPEONATO_ID}`;
    console.log(`[DEBUG] Fetching: ${url}`);

    fetch(url)
        .then(response => response.json())
        .then(result => {
            toggleVisibility('lista-partidas-loader', false);
            console.log('[DEBUG] API Data:', result);

            if (result.status === 'success' && Array.isArray(result.data) && result.data.length > 0) {
                console.log(`[DEBUG] ${result.data.length} partidas encontradas. Renderizando...`);
                renderizarPartidas(result.data);
                
                // --- A MUDANÇA CRUCIAL ESTÁ AQUI ---
                // Força a exibição removendo d-none e adicionando d-flex
                toggleVisibility('lista-partidas-container', true, 'd-flex');
                console.log('[DEBUG SUCESSO] Container de partidas deve estar visível agora.');
                // -----------------------------------

                // Esconde botão de gerar se existir
                toggleVisibility('btnGerarChaves', false, 'd-inline-block');

            } else if (result.status === 'success' && result.data.length === 0) {
                 toggleVisibility('lista-partidas-vazia', true);
            } else {
                 console.error('[ERRO API] Resposta inesperada:', result);
                 const container = document.getElementById('lista-partidas-container');
                 container.innerHTML = '<div class="alert alert-warning">Não foi possível carregar as partidas. Tente recarregar.</div>';
                 toggleVisibility('lista-partidas-container', true, 'd-flex');
            }
        })
        .catch(error => {
            console.error('[ERRO FATAL DE CONEXÃO]', error);
            toggleVisibility('lista-partidas-loader', false);
            const container = document.getElementById('lista-partidas-container');
            container.innerHTML = `<div class="alert alert-danger">Erro de conexão com o servidor.</div>`;
            toggleVisibility('lista-partidas-container', true, 'd-flex');
        });
}

/**
 * Transforma o JSON das partidas em HTML (Cards).
 */
function renderizarPartidas(lista) {
    const container = document.getElementById('lista-partidas-container');
    if(!container) { console.error('[ERRO HTML] Container não achado!'); return; }
    
    container.innerHTML = ''; // Limpa tudo

    try {
        lista.forEach((p, index) => {
            const isFinalizada = p.status === 'finalizada';
            const statusBadge = isFinalizada ?
                '<span class="badge bg-dark">Finalizada</span>' :
                (p.status === 'em_andamento' ? '<span class="badge bg-danger animate-pulse">Ao Vivo</span>' : '<span class="badge bg-secondary">Agendada</span>');

            const cardBorder = isFinalizada ? 'border-secondary opacity-75' : 'border-primary shadow-sm';
            const btnEditClass = isFinalizada ? 'btn-outline-secondary disabled' : 'btn-outline-primary';

            // Tratamento seguro de strings e aspas
            const safeJ1 = (p.j1_display_name || 'A Definir').replace(/"/g, '&quot;').replace(/'/g, "&#39;");
            const safeJ2 = (p.j2_display_name || 'A Definir').replace(/"/g, '&quot;').replace(/'/g, "&#39;");
            
            // Tratamento seguro de IDs e Valores para o onclick
            // Usamos a string 'null' para valores nulos, para passar corretamente no onclick
            const pId = p.partida_id;
            const j1Id = p.j1_inscricao_id !== null ? p.j1_inscricao_id : 'null';
            const j2Id = p.j2_inscricao_id !== null ? p.j2_inscricao_id : 'null';
            const winId = p.vencedor_inscricao_id !== null ? p.vencedor_inscricao_id : 'null';
            const plac1 = p.placar1 !== null ? p.placar1 : 'null';
            const plac2 = p.placar2 !== null ? p.placar2 : 'null';

            const html = `
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 ${cardBorder}">
                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center small fw-bold text-uppercase ls-1">
                        <span>R${p.rodada} - Jogo ${p.ordem_jogo}</span>
                        ${statusBadge}
                    </div>
                    <div class="card-body text-center py-4">
                        <div class="row align-items-center">
                            <div class="col-5 text-truncate">
                                <h6 class="fw-bold mb-0 text-primary text-truncate" title="${safeJ1}">${safeJ1}</h6>
                                ${p.vencedor_inscricao_id == p.j1_inscricao_id && p.j1_inscricao_id !== null ? '<span class="badge bg-success small mt-1">Vencedor</span>' : ''}
                            </div>
                            <div class="col-2">
                                <div class="h3 fw-bold mb-0 text-dark bg-light rounded py-2 border d-flex justify-content-center align-items-center">
                                    <span>${p.placar1 !== null ? p.placar1 : '-'}</span>
                                    <span class="mx-1 small text-muted ms-1 me-1">:</span>
                                    <span>${p.placar2 !== null ? p.placar2 : '-'}</span>
                                </div>
                            </div>
                            <div class="col-5 text-truncate">
                                <h6 class="fw-bold mb-0 text-primary text-truncate" title="${safeJ2}">${safeJ2}</h6>
                                ${p.vencedor_inscricao_id == p.j2_inscricao_id && p.j2_inscricao_id !== null ? '<span class="badge bg-success small mt-1">Vencedor</span>' : ''}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3 pt-0 text-center">
                        <button class="btn btn-sm fw-bold w-75 ${btnEditClass}" 
                                onclick="abrirModalEditarPartida(${pId}, '${safeJ1}', '${safeJ2}', ${plac1}, ${plac2}, ${winId}, ${j1Id}, ${j2Id}, ${p.rodada}, ${p.ordem_jogo})"
                                ${isFinalizada ? 'disabled' : ''}>
                            Resultados
                        </button>
                    </div>
                </div>
            </div>
            `;
            
            container.innerHTML += html;
            console.log(`[DEBUG] HTML da partida ${p.partida_id} injetado.`);
        });
    } catch (e) {
        console.error('[ERRO RENDERIZAÇÃO GERAL]', e);
        container.innerHTML = `<div class="alert alert-danger">Erro visual: ${e.message}</div>`;
        toggleVisibility('lista-partidas-container', true, 'd-flex');
    }
}

/**
 * Ação do botão "Gerar 1ª Rodada".
 */
function gerarChavesTorneio() {
    if (!confirm('ATENÇÃO: Isso vai iniciar o torneio e gerar os confrontos da primeira rodada aleatoriamente. Deseja continuar?')) { return; }
    const btn = document.getElementById('btnGerarChaves');
    if(!btn) return;
    const spinner = btn.querySelector('.spinner-border');
    btn.disabled = true;
    if(spinner) spinner.classList.remove('d-none');

    const token = getAuthToken();
    console.log('[DEBUG] Iniciando geração de chaves...');

    fetch(`${window.LFE_CONFIG.API_URL}/gestor/gerar_chaves.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token },
                body: JSON.stringify({ campeonato_id: window.LFE_CONFIG.CAMPEONATO_ID })
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Erro: ' + (result.message || 'Erro desconhecido.'));
                    btn.disabled = false;
                    if(spinner) spinner.classList.add('d-none');
                }
            })
            .catch(error => {
                alert('Erro de conexão.');
                btn.disabled = false;
                if(spinner) spinner.classList.add('d-none');
            });
}

/**
 * Abre o modal de edição e preenche os dados.
 */
let currentModalPlayers = {};

// Função auxiliar para converter a string 'null' de volta para null real
function formatNull(val) { return val === 'null' || val === null ? null : val; }

function abrirModalEditarPartida(id, j1Nome, j2Nome, placar1, placar2, vencedorId, j1Id, j2Id, rodada, ordem) {
    j1Id = formatNull(j1Id); j2Id = formatNull(j2Id);
    vencedorId = formatNull(vencedorId);
    placar1 = formatNull(placar1); placar2 = formatNull(placar2);

    currentModalPlayers = { j1Id: j1Id, j2Id: j2Id };

    document.getElementById('editPartidaId').value = id;
    document.getElementById('editPartidaTitulo').textContent = `RODADA ${rodada} - JOGO ${ordem}`;

    // Jogador 1
    document.getElementById('editJ1Nome').textContent = j1Nome;
    document.getElementById('editJ1Placar').value = placar1 !== null ? placar1 : '';
    document.getElementById('editJ1Placar').disabled = (j1Id === null);
    document.getElementById('labelVencedorJ1').textContent = j1Nome;
    document.getElementById('radioVencedorJ1').disabled = (j1Id === null);
    document.getElementById('radioVencedorJ1').checked = (vencedorId == j1Id && j1Id !== null);

    // Jogador 2
    document.getElementById('editJ2Nome').textContent = j2Nome;
    document.getElementById('editJ2Placar').value = placar2 !== null ? placar2 : '';
    document.getElementById('editJ2Placar').disabled = (j2Id === null);
    document.getElementById('labelVencedorJ2').textContent = j2Nome;
    document.getElementById('radioVencedorJ2').disabled = (j2Id === null);
    document.getElementById('radioVencedorJ2').checked = (vencedorId == j2Id && j2Id !== null);

    if(typeof bootstrap !== 'undefined') {
        const modalEl = document.getElementById('modalEditarPartida');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    } else {
        alert('Erro: Bootstrap não carregado.');
    }
}

/**
 * Ação do botão "Finalizar Partida" dentro do modal.
 */
function salvarPlacarPartida() {
    const partidaId = document.getElementById('editPartidaId').value;
    const placar1 = document.getElementById('editJ1Placar').value;
    const placar2 = document.getElementById('editJ2Placar').value;

    let vencedorInscricaoId = null;
    if (document.getElementById('radioVencedorJ1').checked) vencedorInscricaoId = currentModalPlayers.j1Id;
    else if (document.getElementById('radioVencedorJ2').checked) vencedorInscricaoId = currentModalPlayers.j2Id;

    // Validação: Se não for BYE, exige placares e vencedor
    if (currentModalPlayers.j1Id && currentModalPlayers.j2Id) {
        if (placar1 === '' || placar2 === '' || vencedorInscricaoId === null) {
            alert("Preencha os placares e selecione quem avançou."); return;
        }
    } else if (vencedorInscricaoId === null) {
         // Caso de BYE, só precisa confirmar quem passa (o único que existe)
         alert("Confirme quem avança."); return;
    }

    const btn = document.getElementById('btnSalvarPlacar');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';

    console.log("[SIMULAÇÃO API] Enviando:", { partidaId, placar1, placar2, vencedorInscricaoId });

    setTimeout(() => {
        alert("Simulação: Placar salvo! (API real no próximo passo)");
        const modalEl = document.getElementById('modalEditarPartida');
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();
        btn.disabled = false; btn.innerHTML = originalText;
        carregarPartidas(); 
    }, 1000);
}