// Arquivo: /js/bracket_render.js
// Objetivo: Buscar dados da API de chaves e renderizar o HTML no container flexbox.

document.addEventListener('DOMContentLoaded', function() {
    if (!CAMPEONATO_ID) {
        showError('ID do campeonato não fornecido.'); return;
    }
    fetchBracketData(CAMPEONATO_ID);
});

function showError(msg) {
    document.getElementById('bracket-loader').classList.add('d-none');
    const errEl = document.getElementById('bracket-error');
    errEl.innerText = msg;
    errEl.classList.remove('d-none');
}

function fetchBracketData(campId) {
    // NOTA: Usando o caminho corrigido /public/
    fetch(`${API_BASE_URL}/public/chaves.php?id=${campId}`)
        .then(r => r.json())
        .then(result => {
            document.getElementById('bracket-loader').classList.add('d-none');
            if (result.status === 'success') {
                renderBracket(result.data.rounds);
            } else {
                showError(result.message || 'Erro ao carregar chave.');
            }
        })
        .catch(e => {
            console.error(e);
            showError('Erro de conexão ao buscar dados da chave.');
        });
}

function renderBracket(roundsData) {
    const container = document.getElementById('bracket-flex-container');
    container.innerHTML = ''; // Limpa

    if (!roundsData || roundsData.length === 0) {
        showError('Nenhuma partida encontrada para exibir.'); return;
    }

    // Loop principal: Cria uma COLUNA para cada RODADA
    roundsData.forEach(round => {
        
        // 1. Cria a coluna da rodada
        const roundCol = document.createElement('div');
        roundCol.className = 'round-column';

        // 2. Cria o cabeçalho (Ex: "Quartas de Final")
        const header = document.createElement('div');
        header.className = 'round-header';
        header.innerText = round.label;
        roundCol.appendChild(header);

        // 3. Loop interno: Cria os CARDS DE JOGO dentro da coluna
        round.matches.forEach(match => {
            roundCol.appendChild(createMatchCard(match));
        });

        // Adiciona a coluna completa ao container principal
        container.appendChild(roundCol);
    });

    // Mostra a área da chave
    document.getElementById('bracket-area').classList.remove('d-none');
}

// Função auxiliar que gera o HTML de um único card de jogo
function createMatchCard(match) {
    const card = document.createElement('div');
    card.className = 'match-card';
    // Guardamos o ID para uso futuro com os conectores
    card.setAttribute('data-match-id', match.partida_id);

    // Define quem é o vencedor para aplicar estilo
    const winnerId = match.vencedor_inscricao_id;

    // HTML interno do card
    // Verifica se o nick é 'A Definir' para aplicar estilo TBD
    const j1Class = (match.j1_id == winnerId && winnerId !== null) ? 'winner' : '';
    const j1NameClass = match.j1_nick === 'A Definir' ? 'tbd' : '';
    
    const j2Class = (match.j2_id == winnerId && winnerId !== null) ? 'winner' : '';
    const j2NameClass = match.j2_nick === 'A Definir' ? 'tbd' : '';

    const placar1 = match.placar1 !== null ? match.placar1 : '-';
    const placar2 = match.placar2 !== null ? match.placar2 : '-';

    card.innerHTML = `
        <div class="player-row ${j1Class}">
            <span class="player-name text-truncate ${j1NameClass}" title="${match.j1_nick}">${match.j1_nick}</span>
            <span class="score-box">${placar1}</span>
        </div>
        <div class="player-row mt-2 ${j2Class}">
            <span class="player-name text-truncate ${j2NameClass}" title="${match.j2_nick}">${match.j2_nick}</span>
            <span class="score-box">${placar2}</span>
        </div>
        <small class="text-muted d-block text-center mt-2" style="font-size: 0.7rem;">Jogo #${match.ordem_jogo}</small>
    `;

    return card;
}