// Arquivo: /painel/js/staff_checkin.js
// Objetivo: Controlar o scanner de QR Code e a validação manual para a equipe de Staff.

let html5QrcodeScanner = null;
let isProcessing = false; // Trava para evitar leituras duplas rápidas

document.addEventListener('DOMContentLoaded', function() {
    initScanner();
    setupManualInput();
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

function showResult(type, title, message) {
    const successAlert = document.getElementById('checkin-result-success');
    const errorAlert = document.getElementById('checkin-result-error');

    // Esconde ambos primeiro
    successAlert.classList.add('d-none');
    errorAlert.classList.add('d-none');

    if (type === 'success') {
        document.getElementById('success-title').textContent = title;
        document.getElementById('success-msg').textContent = message;
        successAlert.classList.remove('d-none');
        // Toca um som de sucesso (opcional, mas útil em eventos)
        // playSuccessSound(); 
    } else {
        document.getElementById('error-title').textContent = title;
        document.getElementById('error-msg').textContent = message;
        errorAlert.classList.remove('d-none');
        // Toca um som de erro
        // playErrorSound();
    }

    // Limpa o resultado após 5 segundos para o próximo da fila
    setTimeout(() => {
        successAlert.classList.add('d-none');
        errorAlert.classList.add('d-none');
    }, 5000);
}

// --- LÓGICA DO SCANNER (CÂMERA) ---
function initScanner() {
    const statusEl = document.getElementById('camera-status');

    // Cria a instância do scanner
    html5QrcodeScanner = new Html5Qrcode("reader");

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    // Prefere a câmera traseira (environment) em celulares
    html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
        .then(() => {
            statusEl.textContent = 'Câmera ativa. Aguardando QR Code...';
            statusEl.classList.remove('text-warning');
            statusEl.classList.add('text-success');
        })
        .catch((err) => {
            console.error("Erro ao iniciar câmera:", err);
            statusEl.textContent = 'Erro ao acessar câmera. Verifique as permissões ou use a entrada manual.';
            statusEl.classList.remove('text-warning');
            statusEl.classList.add('text-danger');
        });
}

function onScanSuccess(decodedText, decodedResult) {
    // Se já estiver processando um código, ignora leituras subsequentes
    if (isProcessing) return;

    console.log(`Código Escaneado: ${decodedText}`);
    performCheckin(decodedText);
}

function onScanFailure(error) {
    // Esta função roda MUITAS vezes por segundo enquanto a câmera não acha um QR code.
    // Não faça nada pesado aqui. Geralmente ignoramos o erro de "não encontrei nada".
    // console.warn(`Erro de leitura (normal): ${error}`);
}

// --- LÓGICA DA ENTRADA MANUAL ---
function setupManualInput() {
    const btn = document.getElementById('btn-manual-checkin');
    const input = document.getElementById('manual-hash-input');

    btn.addEventListener('click', () => {
        const hash = input.value.trim();
        if (hash) {
            performCheckin(hash);
            input.value = ''; // Limpa o campo
        }
    });

    // Permite apertar Enter no campo de texto
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            btn.click();
        }
    });
}

// --- CHAMADA À API (O CÉREBRO) ---
function performCheckin(qrHash) {
    isProcessing = true; // Trava o scanner
    // Mostra feedback visual imediato de que está processando
    showResult('success', 'Processando...', 'Validando código no servidor...');
    const successAlert = document.getElementById('checkin-result-success');
    successAlert.classList.remove('alert-success'); // Remove cor verde temporariamente
    successAlert.classList.add('alert-warning'); // Fica amarelo enquanto processa

    const token = getAuthToken();

    fetch(`${window.LFE_CONFIG.API_URL}/staff/checkin.php`, {
        method: 'POST',
        headers: { 
            'Authorization': 'Bearer ' + token, 
            'Content-Type': 'application/json' 
        },
        body: JSON.stringify({ qr_hash: qrHash })
    })
    .then(r => r.json().then(data => ({ status: r.status, body: data })))
    .then(result => {
        successAlert.classList.remove('alert-warning'); // Remove amarelo
        successAlert.classList.add('alert-success');    // Volta o verde

        if (result.status === 200 && result.body.status === 'success') {
            // SUCESSO!
            const nomeJogador = result.body.data.jogador_nome;
            const nomeCampeonato = result.body.data.campeonato_nome;
            showResult('success', 'Acesso Liberado!', `${nomeJogador} entrou no torneio: ${nomeCampeonato}.`);
        } else {
            // ERRO DA API (Ex: Código inválido, já usado, etc)
            throw new Error(result.body.message || 'Erro desconhecido na validação.');
        }
    })
    .catch(error => {
        console.error('Erro no check-in:', error);
        showResult('error', 'Acesso Negado', error.message);
    })
    .finally(() => {
        // Destrava o scanner após 2 segundos para permitir o próximo da fila
        setTimeout(() => {
            isProcessing = false;
        }, 2000);
    });
}