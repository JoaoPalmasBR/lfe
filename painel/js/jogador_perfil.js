// Arquivo: /painel/js/jogador_perfil.js
// Objetivo: Controlar a visualização e edição do perfil do jogador.

document.addEventListener('DOMContentLoaded', function() {
    // Carrega os dados assim que a página abre
    carregarDadosPerfil();
    
    // Configura o listener do formulário (ainda sem função real de salvar)
    document.getElementById('form-perfil').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('A funcionalidade de salvar será implementada no próximo passo!');
    });

    // Configura o listener do upload de foto (ainda sem função real)
    document.getElementById('avatar-upload-input').addEventListener('change', function(e) {
        alert('A funcionalidade de upload de foto será implementada em breve!');
        // Reset para não ficar o arquivo selecionado
        this.value = '';
    });
});

// --- FUNÇÕES PRINCIPAIS ---

async function carregarDadosPerfil() {
    const loader = document.getElementById('profile-loader');
    const content = document.getElementById('profile-content');
    
    try {
        // Usa a função auxiliar global (definida no layout principal) para pegar o token
        const token = getAuthToken(); 
        
        const response = await fetch(`${window.LFE_CONFIG.API_URL}/jogador/meu_perfil.php`, {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            // SUCESSO: Preenche o formulário com os dados recebidos
            popularFormulario(result.data);
            // Mostra o conteúdo e esconde o loader
            loader.classList.add('d-none');
            content.classList.remove('d-none');
        } else {
            throw new Error(result.message || 'Erro ao carregar dados do perfil.');
        }

    } catch (error) {
        console.error('Erro perfil:', error);
        loader.classList.add('d-none');
        mostrarFeedbackPerfil('error', 'Não foi possível carregar seus dados. Tente recarregar a página.');
    }
}

function popularFormulario(data) {
    // 1. Preenche o Resumo (Coluna Esquerda)
    document.getElementById('resumo-nickname').textContent = data.nickname || 'Sem Nickname';
    document.getElementById('resumo-nome-completo').textContent = data.nome_completo;
    document.getElementById('resumo-pontos').textContent = data.pontuacao_total || '0';
    
    // Define o avatar (usa um placeholder se estiver vazio)
    const avatarUrl = data.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(data.nome_completo)}&background=random&color=fff&size=150`;
    document.getElementById('avatar-preview').src = avatarUrl;

    // 2. Preenche a Aba "Perfil Gamer"
    // Usa o operador '|| ""' para garantir que não apareça "null" nos campos vazios
    document.getElementById('input-nickname').value = data.nickname || '';
    document.getElementById('input-biografia').value = data.biografia || '';
    document.getElementById('input-discord').value = data.link_discord || '';
    document.getElementById('input-twitch').value = data.link_twitch || '';
    document.getElementById('input-instagram').value = data.link_instagram || '';

    // 3. Preenche a Aba "Dados Pessoais"
    document.getElementById('input-nome-completo').value = data.nome_completo || '';
    document.getElementById('input-email').value = data.email || '';
    document.getElementById('input-cpf').value = data.cpf || '';
    document.getElementById('input-telefone').value = data.telefone || '';
    document.getElementById('input-data-nascimento').value = data.data_nascimento || '';
    document.getElementById('input-cidade').value = data.cidade || '';
}

// Função auxiliar para mostrar alertas na tela
function mostrarFeedbackPerfil(tipo, mensagem) {
    const feedbackEl = document.getElementById('profile-feedback');
    const msgEl = document.getElementById('profile-feedback-msg');
    
    feedbackEl.classList.remove('alert-success', 'alert-danger', 'd-none');
    feedbackEl.classList.add(tipo === 'success' ? 'alert-success' : 'alert-danger');
    msgEl.textContent = mensagem;
    
    // Esconde automaticamente após 5 segundos
    setTimeout(() => {
        feedbackEl.classList.add('d-none');
    }, 5000);
}