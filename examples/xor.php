#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Noiselabs\Byonn\Activation;
use Noiselabs\Byonn\CostFunction;
use Noiselabs\Byonn\Debug\Debugger;
use Noiselabs\Byonn\Initializer;
use Noiselabs\Byonn\Optimizer;
use Noiselabs\Byonn\TrainingSet;
use Noiselabs\Byonn\Topology;
use Noiselabs\Byonn\NeuralNetwork;

$xorTrainingSet = new TrainingSet(
    [[0, 0], [0, 1], [1, 0], [1, 1]],
    [0, 1, 1, 0]
);

$trainingIterations = 50000;

$neuralNetwork = new NeuralNetwork(
    new Topology([2, 2, 1], [
        new Activation\Sigmoid(),
        new Activation\Sigmoid(),
    ]),
    new Initializer\ParametersInitializer(
        new Initializer\Zeros(),
        new Initializer\RandomUniform(0, 1)
    ),
    new Optimizer\GradientDescent(0.1),
    new CostFunction\MeanSquaredError(),
    new Debugger(__DIR__ . '/../build/xor-training.json', $trainingIterations / 100)
);

$neuralNetwork->train($xorTrainingSet, $trainingIterations, 0.001);

echo "\n\nPredictions:";
$inputs = $xorTrainingSet->getInputs();
$targets = $xorTrainingSet->getTargets();
$pass = 0;
for ($i = 0; $i < count($inputs); $i++) {
    $predicted = $neuralNetwork->predict($inputs[$i]->getVector());
    $y = $targets[$i]->get(0);
    $h = $predicted[0];
    echo sprintf("\n* Input: [%s], Predicted: %s, Expected: %s",
        implode(', ', $inputs[$i]->getVector()), $h, $y);
    if (abs($y - $h) < 0.5) {
        $pass++;
        echo (' [passed]');
    } else {
        echo (' [failed]');
    }
}
echo sprintf("\nAccuracy: %d%%", $pass/count($inputs)*100);
