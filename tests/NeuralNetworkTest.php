<?php

namespace Noiselabs\Byonn\Test;

use Noiselabs\Byonn\Activation;
use Noiselabs\Byonn\CostFunction;
use Noiselabs\Byonn\Initializer;
use Noiselabs\Byonn\Math\MatrixFunctions as M;
use Noiselabs\Byonn\NeuralNetwork;
use Noiselabs\Byonn\Optimizer;
use Noiselabs\Byonn\TrainingSet;
use Noiselabs\Byonn\Topology;
use PHPUnit\Framework\TestCase;

class NeuralNetworkTest extends TestCase
{
    public function test_parameters_initialization_with_a_single_neuron()
    {
        $neuralNetwork = new NeuralNetwork(
            new Topology([2, 1],[new Activation\Sigmoid()]),
            new Initializer\ParametersInitializer(
                new Initializer\Zeros(),
                new Initializer\RandomUniform(0, 1)
            ),
            new Optimizer\GradientDescent(),
            new CostFunction\MeanSquaredError()
        );
        $parameters = $neuralNetwork->getParameters();

        self::assertInternalType('array', $parameters->b);
        self::assertCount(1, $parameters->b);

        self::assertInternalType('array', $parameters->w);
        self::assertCount(1, $parameters->w);
    }

    public function test_parameters_initialization_with_multiple_hidden_layers()
    {
        $topology = new Topology([2, 3, 5, 3, 1], [new Activation\Sigmoid()]);
        $neuralNetwork = new NeuralNetwork(
            $topology,
            new Initializer\ParametersInitializer(
                new Initializer\Zeros(),
                new Initializer\RandomUniform(0, 1)
            ),
            new Optimizer\GradientDescent(),
            new CostFunction\MeanSquaredError()
        );

        $parameters = $neuralNetwork->getParameters();

        self::assertInternalType('array', $parameters->b);
        self::assertInternalType('array', $parameters->w);
        self::assertCount(4, $parameters->w);
        self::assertCount(4, $parameters->b);

        foreach (array_keys($parameters->w) as $l) {
            M::assertDimensions($parameters->b[$l], [$topology->neuronsCount($l), 1]);
            M::assertDimensions($parameters->w[$l], [$topology->neuronsCount($l), $topology->neuronsCount($l-1)]);
        }
    }

    public function test_it_can_train_on_xor()
    {
        $inputs = [
            [0, 0], // 0
            [0, 1], // 1
            [1, 0], // 1
            [1, 1], // 0
        ];
        $targets = [0, 1, 1, 0];

        $neuralNetwork = new NeuralNetwork(
            new Topology([2, 3, 1], [new Activation\Sigmoid()]),
            new Initializer\ParametersInitializer(
                new Initializer\Zeros(),
                new Initializer\RandomUniform(-1, 1)
            ),
            new Optimizer\GradientDescent(),
            new CostFunction\MeanSquaredError()
        );

        $this->setOutputCallback(function() {});
        $neuralNetwork->train(new TrainingSet($inputs, $targets), 2);

        $prediction = $neuralNetwork->predict([0, 0]);
        self::assertInternalType('array', $prediction);
        self::assertCount(1, $prediction);

        $p = $neuralNetwork->getParameters();
        foreach (array_keys($p->w) as $l) {
            M::assertDimensions($p->da[$l], $p->a[$l]);
            M::assertDimensions($p->dw[$l], $p->w[$l]);
            M::assertDimensions($p->db[$l], $p->b[$l]);
        }
    }
}
