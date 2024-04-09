<?php

require_once('vendor/autoload.php');

use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\Dataset\CsvDataset;

$vectorizer = new TokenCountVectorizer(new WordTokenizer());

$dataset = new CsvDataset('data/languages.csv', 1);

$samples = [];
$originalSamples = [];
$targets = [];

foreach ($dataset->getSamples() as $sample) {
    $samples[] = $sample[0];
    $originalSamples[] = $sample[0];
}
foreach ($dataset->getTargets() as $target) {
    $targets[] = $target;
}

$vectorizer->fit($samples);
$vectorizer->transform($samples);

$dataset = new ArrayDataset($samples, $targets);

$randomSplit = new StratifiedRandomSplit($dataset, 0.8);

$classifier = new SVC(Kernel::RBF, 10000);
$classifier->train($randomSplit->getTrainSamples(), $randomSplit->getTrainLabels());

$predictedLabels = $classifier->predict($randomSplit->getTestSamples());

print_r($originalSamples);
echo "<br><br><br>";
$testSamples = $randomSplit->getTestSamples();
$testLabels = $randomSplit->getTestLabels();
for ($i = 0; $i < count($testLabels); $i++) {
    echo 'Palabra: ' . $originalSamples[$i] . PHP_EOL;
    echo 'Predicción: ' . $predictedLabels[$i] . PHP_EOL;
    if ($testLabels[$i] == $predictedLabels[$i]) {
        echo $i . ' pass' . PHP_EOL;
    } else {
        echo $i . ' fail' . PHP_EOL;
    }
}

echo 'Accuracy: ' . (Accuracy::score($randomSplit->getTestLabels(), $predictedLabels)) * 100 . '%' . PHP_EOL;



// Nuevas frases para predecir

$newSamples = [
    'do you know what time?',
    'Per favore, quanto costa spedire'
];
echo "<br><br><br> virgen: ";
print_r($newSamples);
$vectorizer->fit($newSamples);
echo "<br><br><br> fit: ";
print_r($newSamples);
$vectorizer->transform($newSamples);
echo "<br><br><br> transform: ";
print_r($newSamples);
$newPredictions = $classifier->predict($newSamples);
echo "<br><br><br> predict: ";
print_r($newSamples);

echo "<br><br><br> prediction: <br> ";
foreach ($newSamples as $i => $newSample) {
    echo 'Frase: ' . $newSample[0] . PHP_EOL;
    echo 'Predicción: ' . $newPredictions[$i] . PHP_EOL;
}

?>