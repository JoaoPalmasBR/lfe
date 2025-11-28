<?php
// Define o caminho do diretório que você deseja listar.
// Substitua '.' pelo caminho desejado, por ex. '/var/www/html/meus_arquivos'
$diretorio_raiz = '.';

try {
    $iterator_diretorio = new RecursiveDirectoryIterator(
        $diretorio_raiz,
        RecursiveDirectoryIterator::SKIP_DOTS // Ignora '.' e '..'
    );

    $iterator_arvore = new RecursiveIteratorIterator(
        $iterator_diretorio,
        RecursiveIteratorIterator::SELF_FIRST // Lista o diretório antes de seus filhos
    );

    echo "<pre>"; // Mantém a formatação da árvore ASCII
    foreach ($iterator_arvore as $item) {
        // Usa getPathname() para obter o caminho completo formatado
        echo $item->getPathname() . "\n";
    }
    echo "</pre>";
} catch (UnexpectedValueException $e) {
    echo "Erro ao abrir o diretório: " . $e->getMessage();
}
