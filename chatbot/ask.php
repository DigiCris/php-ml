<?php
$start = microtime(true);
require_once('vendor/autoload.php');

use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;

use Phpml\Serialization\Serializer;

$vectorizer = new TokenCountVectorizer(new WordTokenizer());

$classifier = new SVC(Kernel::RBF, 10000);
$classifier = leerClasificador("casificador.dat");
//$classifier = include 'clasificador.php';

// Nuevas frases para predecir

$newSamples = [
    'cuanto es el minimo a invertir?',
    'que es comunyt?'
];
//echo "<br><br><br> virgen: ";
//print_r($newSamples);
$vectorizer->fit($newSamples);
//echo "<br><br><br> fit: ";
//print_r($newSamples);
$vectorizer->transform($newSamples);
//echo "<br><br><br> transform: ";
//print_r($newSamples);
print_r($classifier);

$newPredictions = $classifier->predict($newSamples);
echo "<br><br><br> predict: ";
//print_r($newSamples);

echo "<br><br><br> prediction: <br> ";
foreach ($newSamples as $i => $newSample) {
    echo 'Frase: ' . $newSample[$i] . PHP_EOL;
    echo 'Predicción: ' . $newPredictions[$i] . PHP_EOL;
}

$end = microtime(true);


echo "Tiempo de ejecución: " . $end-$start . " segundos<br><br>";



function guardarClasificador($nombreArchivo, $clasificador) {
    // Serializar el clasificador
    $serializedClassifier = serialize($clasificador);
    
    // Calcular el hash del contenido serializado
    $hash = md5($serializedClassifier);
    
    // Crear un array asociativo con el hash y el contenido serializado
    $data = array(
        'hash' => $hash,
        'content' => $serializedClassifier
    );
    
    // Convertir el array en formato JSON
    $jsonData = json_encode($data);
    
    // Guardar el JSON en el archivo
    if (file_put_contents($nombreArchivo, $jsonData) === false) {
        throw new Exception("Error al guardar el archivo.");
    }
}

function leerClasificador($nombreArchivo) {
    // Leer el contenido del archivo
    $jsonData = file_get_contents($nombreArchivo);
    
    if ($jsonData === false) {
        throw new Exception("Error al leer el archivo.");
    }
    
    // Decodificar el JSON en un array asociativo
    $data = json_decode($jsonData, true);
    
    if ($data === null) {
        throw new Exception("Error al decodificar el JSON.");
    }
    
    // Obtener el hash y el contenido del array
    $hash = $data['hash'];
    $serializedClassifier = $data['content'];
    
    // Verificar la integridad del contenido
    if (md5($serializedClassifier) !== $hash) {
        throw new Exception("El contenido del archivo ha sido modificado.");
    }
    
    // Deserializar el contenido en un clasificador
    $clasificador = unserialize($serializedClassifier);
    
    // Devolver el clasificador cargado
    return $clasificador;
}

?>