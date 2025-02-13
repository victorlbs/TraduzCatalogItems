<?php
// Caminho para o arquivo XML
$xmlFile = 'furnidata.xml'; // Substitua pelo caminho do seu arquivo XML

// Carrega o XML
$xml = simplexml_load_file($xmlFile);

if ($xml === false) {
    die("Erro ao carregar o arquivo XML");
}

// Conexão com o banco de dados
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'habbo';

$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Array para armazenar os registros modificados
$modifiedItems = [];

// Loop por cada `furnitype` no XML
foreach ($xml->roomitemtypes->furnitype as $furni) {
    $classname = $furni['classname']; // Pega o atributo "classname" (catalog_name)
    $name = $furni->name; // Pega o valor do campo <name>

    // Atualiza o campo catalog_name no banco de dados com o nome do XML
    $stmt = $conn->prepare("UPDATE catalog_items SET catalog_name = ? WHERE catalog_name = ?");
    $stmt->bind_param('ss', $name, $classname);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            // Adiciona ao array os registros que foram modificados
            $modifiedItems[] = [
                'catalog_name' => (string)$classname,
                'new_catalog_name' => (string)$name
            ];
            echo "Atualizado com sucesso: $classname -> $name\n";
        } else {
            echo "Nenhum registro encontrado para: $classname\n";
        }
    } else {
        echo "Erro ao atualizar: " . $stmt->error . "\n";
    }
}

// Exibe o array de registros modificados
echo "\nRegistros modificados:\n";
print_r($modifiedItems);

// Fecha conexão
$conn->close();
?>
