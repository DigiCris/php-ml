<?php
$start = microtime(true);
if(empty($_POST))
{
    $_POST = json_decode(file_get_contents('php://input'), true);
}

// Verificar si se recibió una pregunta
if (isset($_POST['pregunta'])) {
    // Obtener la pregunta enviada desde JavaScript
    $pregunta = $_POST['pregunta'];
    $newSamples = [''];
    $newSamples[0] = $pregunta;
  } else {
    // No se recibió ninguna pregunta, devolver un mensaje de error
    echo "No se recibió ninguna pregunta. Intentelo nuevamente más tarde!";
    exit;
  }

  $respuestas = array(
    "Comuny-T es una plataforma de tokenización que permite a los usuarios realizar inversiones en diferentes tipos de proyectos, de forma fácil, rápida y segura, ya que todas las transacciones están alojadas en la red blockchain.
    intermediamos entre el desarrollador de cada proyecto y los miembros de nuestra comunidad para democratizar la barrera de acceso a negocios hasta ahora reservados a grandes tickets.  Nuestro equipo de profesionales analiza diferentes posibilidades de inversión , realizando un estudio económico/financiero , de riesgo y legal de cada proyecto, para luego presentarlo a la comunidad. 
    Los ingresos para el mantenimiento de la plataforma se obtienen mediante el cobro de una comisión del 5% a los inversores y otro  5% a los desarrolladores,  sobre el fondeo de cada proyecto.
    ",
    "Comuny-T permite a cualquier persona y desde cualquier lugar del mundo invertir  en los proyectos disponibles en la plataforma. Cualquier usuario mayor de edad puede registrarse e invertir tras pasar el proceso de KYC.",
    "Es muy rápido y fácil, te puede llevar unos pocos minutos y 3 sencillos pasos. Registrate  realizando el proceso de validación de usuario (KYC). Ingresa dinero a la plataforma. Elige el proyecto en el cual invertir y compra tus tokens de proyecto.
    Tanto para el ingreso de dinero como para la compra de tokens de proyectos será necesario que firmes los correspondientes contratos de adhesión. 
    ",
    "Podes invertir con pesos argentinos o dólares mediante transferencia bancaria. También con USDT. Al finalizar el proceso de registro, se te asignará automáticamente una wallet digital para que, en el caso de querer invertir a través de USDT, puedas hacerlo enviando allí el dinero.   
    Al ingresar dinero a la plataforma se te asignaran CTUSD (Comuny-T tokens) en paridad 1=1 con el dólar, de manera que tus tenencias estarán siempre dolarizadas.
    En el caso de los pesos argentinos se transformaran a dólar mep(para proyectos en argentina) o contado con liquidación (para proyectos en el exterior) al valor oficial del día en que se acredite la transferencia. La acreditación del dinero puede demorar hasta un máximo de 48h hábiles.
    ",
    "Un token es la representación digital de un activo en la Blockchain. Tokenización es el acto de digitalizar un activo de manera que el token capture y represente el valor de ese bien y quede registrado en dicha Blockchain.",
    "Accede a nuestra plataforma,  pulsa en “Regístrarme” y a continuación te guiaremos en el proceso. Tendrás que adjuntar una copia de tu documento y validar tu identidad.",
    "En Comuny-T eliminamos la barrera de acceso a las inversiones. Todo miembro de nuestra comunidad puede invertir desde USD1 en cualquier proyecto disponible dentro de nuestra plataforma. ",
    "Cuando tengas dinero disponible en la plataforma y quieras realizar un cash out, podes hacerlo ingresando a “retirar dinero” dentro de tu perfil de usuario. El costo de comisión por retiro es de 1.2%, con un mínimo de USD2. La operación puede demorar un máximo de 48h hábiles.",
    "No hay forma de eliminar por completo el riesgo en las inversiones, pero existen estrategias para minimizar ese riesgo. 
    Antes de realizar cualquier inversión, investiga exhaustivamente el activo en el que estás interesado. Comprende su rendimiento histórico, los factores que lo afectan y las perspectivas futuras. Cuanto más sepas sobre tus inversiones, mejores decisiones vas a tomar. 
    No es recomendable poner todos los huevos en una sola canasta. Distribuí tu capital entre diferentes tipos de activos. Esto ayuda a reducir la exposición a eventos negativos que puedan afectar a un solo activo.
    Antes de realizar una inversión, evalúa cuidadosamente el riesgo potencial en comparación con la recompensa esperada. Busca activos que ofrezcan un equilibrio adecuado entre riesgo y potencial de rendimiento.
    Nuestro equipo de profesionales realiza un exhaustivo análisis de cada proyecto, su viabilidad, riesgos, retornos estimados, estructura jurídica-legal, entre otros aspectos, para que cada usuario cuente con más y mejores herramientas a la hora de decidir realizar una inversión.
    ",
    "Por cada proyecto fondeado, Comuny-T cobra un 5% de comisión sobre el dinero aportado por los inversores. En el caso de retiro de dinero , la comisión es del 1.2%, con un mínimo de USD2.",
    "Hola, Encantado. Soy COMUNY-BOT y estoy aquí para ayudarte. Pregunta lo que deseas saber!!!.",
    "Encantado en ayudar. Espero verte pronto. Exitos en tus inversiones!!!.",
    "Puedes comunicarte por whatsapp al +54911 12345678!!!."
  );






require_once('vendor/autoload.php');

use Phpml\Dataset\ArrayDataset;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WordTokenizer;
use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Metric\Accuracy;
use Phpml\Classification\SVC;
use Phpml\SupportVectorMachine\Kernel;
use Phpml\Dataset\CsvDataset;
use Phpml\Serialization\Serializer;

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

$randomSplit = new StratifiedRandomSplit($dataset, 0.01);

$classifier = new SVC(Kernel::RBF, 10000);
$classifier->train($randomSplit->getTrainSamples(), $randomSplit->getTrainLabels());

//Calculo de accuracy
/*
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
*/


// Nuevas frases para predecir

$newSamples[0] = limpiarString($newSamples);
//print_r($newSamples);
$vectorizer->fit($newSamples);
$vectorizer->transform($newSamples);
$newPredictions = $classifier->predict($newSamples);

//echo "<br><br><br> prediction: <br> ";
foreach ($newSamples as $i => $newSample) {
    //echo 'Frase: ' . $newSample[$i] . PHP_EOL;
    //echo 'Predicción: ' . $newPredictions[$i] . PHP_EOL;
    echo $respuestas[$newPredictions[$i]-1];
}
$end = microtime(true);

//echo "Tiempo de ejecución: " . $end-$start . " segundos<br><br>";


function limpiarString($string) {
    // Eliminar caracteres no alfabéticos
    //print_r($string);
    $string = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ ]/u', '', $string);
    //print_r($string);
    // Convertir mayúsculas a minúsculas
    $string = mb_strtolower($string[0], 'UTF-8');
    //print_r($string);
    // Remover tildes
    $string = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'Ñ'],
        ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'n', 'n'],
        $string
    );
    $string = eliminarPalabras($string);
    //print_r($string);
    // Reemplazar múltiples espacios por uno solo
    $string = preg_replace('/\s+/', ' ', $string);
    //print_r($string);
    // Eliminar espacios al principio y al final
    $string = trim($string);
    return $string;
}



function eliminarPalabras($texto) {
    $palabras = array(
      "a", "al", "ante", "bajo", "cabe", "con", "contra", "de", "desde", "durante",
      "en", "entre", "hacia", "hasta", "mediante", "para", "por", "segun", "sin",
      "sobre", "tras", "salvo", "como", "pero", "mas", "menos", "muy", "mucho",
      "poco", "tambien", "tampoco", "ya", "aun", "si", "no", "si", "o", "u", "ni",
      "que", "quien", "cual", "como", "cuando", "donde", "por que", "para que",
      "con que", "de que", "quienes", "cuales", "cuanto", "cuantos", "cuanta",
      "cuantas", "todo", "todos", "toda", "todas", "otro", "otros", "otra", "otras",
      "mismo", "mismos", "misma", "mismas", "tal", "tales", "cierto", "ciertos",
      "cierta", "ciertas", "aquel", "aquellos", "aquella", "aquellas", "este",
      "estos", "esta", "estas", "ese", "esos", "esa", "esas", "uno", "unos", "una",
      "unas", "mucho", "muchos", "mucha", "muchas", "poco", "pocos", "poca", "pocas",
      "todo", "todos", "toda", "todas", "varios", "varias", "tanto", "tantos",
      "tanta", "tantas", "cualquier", "cualesquier", "cuanta", "cuantas", "cuantos",
      "cuantas", "cuanto", "cuantos", "todo", "todos", "toda", "todas", "ninguno",
      "ninguna", "ningunos", "ningunas", "ninguno", "ningunos", "ninguna", "ningunas",
      "otro", "otros", "otra", "otras"
    );
  
    // Dividir el texto en palabras
    $palabrasTexto = preg_split('/\s+/', $texto);
  
    // Recorrer las palabras del texto
    foreach ($palabrasTexto as &$palabra) {
      // Convertir la palabra a minúsculas para hacer la comparación
      $palabra = mb_strtolower($palabra, 'UTF-8');
  
      // Verificar si la palabra está en el array de palabras a eliminar
      if (in_array($palabra, $palabras)) {
        // Eliminar la palabra del texto
        $palabra = "";
      }
    }
  
    // Unir las palabras nuevamente en un texto sin las palabras eliminadas
    $textoSinPalabras = implode(" ", $palabrasTexto);
  
    // Eliminar espacios en blanco adicionales
    $textoSinPalabras = trim(preg_replace('/\s+/', ' ', $textoSinPalabras));
  
    // Devolver el texto sin las palabras eliminadas
    return $textoSinPalabras;
  }





?>