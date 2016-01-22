<?php

use Majora\WorkshopReactPhp\BlurTransformer;
use Majora\WorkshopReactPhp\EmbossTransformer;
use Majora\WorkshopReactPhp\ImageTransformerInterface;
use Majora\WorkshopReactPhp\ThresholdTransformer;

require __DIR__.'/../vendor/autoload.php';

set_time_limit(120);

$tmp_name = $_FILES["fileInput"]["tmp_name"];
$imagePath = realpath(__DIR__.'/../tmp').'/picture.jpg';
if(! move_uploaded_file($tmp_name, $imagePath)) {
    exit('upload error');
}


$results = [];

$loop = React\EventLoop\Factory::create();

foreach ([
    new ThresholdTransformer(),
    new BlurTransformer(),
    new EmbossTransformer(),
] as $transformer) {
    /* @var ImageTransformerInterface $transformer */
    $results[] = $transformer->transform($imagePath);
}

?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Workshop ReactPhp result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>ReactPhp Workshop result</h1>
    <?php foreach ($results as $result) : ?>
    <img src="data:image/jpg;base64,<?php echo base64_encode($result); ?>">
    <?php endforeach; ?>
</div>
</body>
</html>
