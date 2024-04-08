
<?php

require "./vendor/autoload.php";

use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Regression\LeastSquares;
use Phpml\Metric\Regression;

// cargo la data del csv
$data = new CsvDataset("./data/insurance.csv", 1, true);

//Genero dataset y trainSet
$dataSet = new RandomSplit($data,0.2,156); //(datos, porcentage a tomar, seed para que tome de data)

//$dataSet->getTrainSamples();
//$dataSet->getTrainLabels();
//$dataSet->getTestSamples();
//$dataSet->getTestLabels();


//entrenando la IA
$regression = new LeastSquares();
$regression->train($dataSet->getTrainSamples(), $dataSet->getTrainLabels());

// Predigo los valores de la train sample en funcion del entrenamiento dado anteriormente con la tecnica de cuadrados minimos
$predict = $regression->predict($dataSet->getTestSamples());

// califico el entrenamiento comparando la correlación de los valores de la regrsión con los verdaderos en testSamples
$score = Regression::r2Score($dataSet->getTestLabels(), $predict);

echo "score is: ".$score;
//print_r($predict);

?>

<?php
/*
require "./vendor/autoload.php";
use Phpml\Dataset\CsvDataset;
use Phpml\CrossValidation\RandomSplit;
use Phpml\Regression\LeastSquares;
use Phpml\Metric\Regression;

$data = new CsvDataset("./data/insurance.csv", 1, true);
$dataSet = new RandomSplit($data, 0.2, 156);

$regression = new LeastSquares();
$regression->train($dataSet->getTrainSamples(), $dataSet->getTrainLabels());
$predict = $regression->predict($dataSet->getTestSamples());
$score = Regression::r2Score($dataSet->getTestLabels(), $predict);

echo "Score is: " . $score;
*/
?>