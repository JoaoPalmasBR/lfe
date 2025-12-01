// Arquivo: /painel/js/utils.js
// Objetivo: Funções utilitárias globais reutilizáveis em todo o painel.

/**
 * Lê o token JWT salvo no cookie 'lfe_token'.
 * @returns {string} O token ou uma string vazia se não encontrar.
 */
function getAuthToken() {
    const name = "lfe_token=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

// Você pode adicionar outras funções globais aqui no futuro
// ex: formatação de data, moeda, etc.