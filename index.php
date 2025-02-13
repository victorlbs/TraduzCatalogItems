<?php

$xmlFile = 'furnidata.xml'; 

$xml = simplexml_load_file($xmlFile);

if ($xml === false) {
    die("Erro ao carregar o arquivo XML");
}


$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'habbo';

$conn = new mysqli($host, $user, $pass, $dbname);


if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


$modifiedItems = [];

// Loop por cada `furnitype` no XML
foreach ($xml->roomitemtypes->furnitype as $furni) {
    $classname = $furni['classname']; 
    $name = $furni->name;

 
    $stmt = $conn->prepare("UPDATE catalog_items SET catalog_name = ? WHERE catalog_name = ?");
    $stmt->bind_param('ss', $name, $classname);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
    
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


echo "\nRegistros modificados:\n";
print_r($modifiedItems);

// Fecha conexão
$conn->close();
?>
