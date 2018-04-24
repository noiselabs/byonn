<?php

namespace Noiselabs\Byonn;

use DateTime;
use MathPHP\LinearAlgebra\Vector;
use Noiselabs\Byonn\Activation\Activation;
use Noiselabs\Byonn\CostFunction\CostFunction;
use Noiselabs\Byonn\Debug\Debugger;
use Noiselabs\Byonn\Initializer\ParametersInitializer;
use Noiselabs\Byonn\Math\MatrixFunctions as M;
use Noiselabs\Byonn\Optimizer\Optimizer;
use Webmozart\Assert\Assert;

class NeuralNetwork
{
    /**
     * @var Parameters
     */
    private $p;

    /**
     * @var Topology
     */
    private $topology;

    /**
     * @var array|Activation[]
     */
    private $activations;

    /**
     * @var CostFunction
     */
    private $costFunction;

    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * @var Optimizer
     */
    private $optimizer;

    /**
     * @var ParametersInitializer
     */
    private $initializer;

    public function __construct(
        Topology $topology,
        ParametersInitializer $initializer,
        Optimizer $optimizer,
        CostFunction $costFunction,
        ?Debugger $debugger = null
    ) {
        $this->topology = $topology;
        $this->activations = $topology->getActivations();
        $this->initializer = $initializer;
        $this->optimizer = $optimizer;
        $this->costFunction = $costFunction;
        $this->debugger = $debugger;

        $this->initializeParameters();
    }

    public function train(TrainingSet $dataset, int $maxTrainingIterations = 20000, float $maxError = 0.01)
    {
        Assert::greaterThanEq($maxTrainingIterations, 1);

        $inputs = $dataset->getInputs();
        $targets = $dataset->getTargets();

        $epoch = 0;
        $error = INF;

        $this->debugger ? $this->debugger->logPreTraining($this->p) : null;

        $started = new DateTime();
        echo sprintf("Training for %d epochs or until the cost falls below %f...\n\n", $maxTrainingIterations, $maxError);

        while ($epoch < $maxTrainingIterations && $error > $maxError) {
            $epoch++;
            $costs = [];

            for ($i = 0; $i < $dataset->getSize(); $i++) {
                // 1. Forward propagation
                $prediction = $this->doForwardPropagation($inputs[$i]);

                // 2. Compute cost
                $costs[$i] = $this->computeCost($prediction, $targets[$i]);

                // 3. Backward propagation
                $this->doBackPropagation($targets[$i]);

                // 4. Update parameters (gradient descent)
                $this->updateParameters();

                if ($this->debugger) {
                    $this->debugger->logLearningStep($epoch, $i + 1, $inputs[$i], $targets[$i], $costs[$i], $this->p);
                }
            }

            $error = array_sum($costs) / count($costs);

            if ($epoch % 100 == 0) {
                echo sprintf("* Epoch: %d, Error: %f\n", $epoch, $error);
            }
        }

        $finished = new DateTime();
        echo sprintf("\n...done.\nEpochs: %d, Error: %f (took %.2f seconds).", $epoch, $error,
            ($finished->getTimestamp() - $started->getTimestamp()));

        $this->debugger ? $this->debugger->generateHtmlReport() : null;
    }

    public function predict(array $input): array
    {
        return $this->doForwardPropagation(new Vector($input))->getVector();
    }

    public function overrideParameters(Parameters $p): void
    {
        $this->p = $p;
    }

    public function getParameters(): Parameters
    {
        return $this->p;
    }

    private function initializeParameters(): void
    {
        $this->p = new Parameters();

        for ($l = 1; $l <= $this->topology->layerCount(); $l++) {
            $m = $this->topology->neuronsCount($l);
            $n = $this->topology->neuronsCount($l - 1);

            $this->p->b[$l] = $this->initializer->initializeBiases($m, 1);
            $this->p->w[$l] = $this->initializer->initializeWeights($m, $n);
        }
    }

    private function doForwardPropagation(Vector $input): Vector
    {
        $layerCount = $this->topology->layerCount();

        // To ease our computations we will consider the network inputs to be "layer-0" activations.
        $this->p->a[0] = $input->asColumnMatrix();

        for ($l = 1; $l <= $layerCount; $l++) {
            $this->p->z[$l] = M::dot($this->p->w[$l], $this->p->a[$l - 1])->add($this->p->b[$l]);
            $this->p->a[$l] = $this->activations[$l]->compute($this->p->z[$l]);
        }

        $prediction = $this->p->a[$layerCount]->asVectors();
        Assert::count($prediction, 1);
        $prediction = reset($prediction);

        return $prediction;
    }

    private function computeCost(Vector $prediction, Vector $target): float
    {
        return $this->costFunction->compute($prediction, $target);
    }

    private function doBackPropagation(Vector $target): void
    {
        $layerCount = $this->topology->layerCount();
        $l = $layerCount;

        // Calculate the post-activation gradient for the output layer
        $this->p->da[$l] = $this->costFunction->differentiate($this->p->a[$l], $target->asColumnMatrix());
        $this->p->dz[$l] = M::multiply($this->p->da[$l], $this->activations[$l]->differentiate($this->p->z[$l]));
        $this->p->dw[$l] = M::dot($this->p->dz[$l], $this->p->a[$l - 1]->transpose());
        $this->p->db[$l] = $this->p->dz[$l];

        for ($l = ($layerCount - 1); $l >= 1; $l--) {
            $this->p->da[$l] = M::dot($this->p->w[$l + 1]->transpose(), $this->p->dz[$l + 1]);
            $this->p->dz[$l] = M::multiply($this->p->da[$l], $this->activations[$l]->differentiate($this->p->z[$l]));
            $this->p->dw[$l] = M::dot($this->p->dz[$l], $this->p->a[$l - 1]->transpose());
            $this->p->db[$l] = $this->p->dz[$l];
        }
    }

    private function updateParameters(): void
    {
        for ($l = 1; $l <= $this->topology->layerCount(); $l++) {
            $this->p->b[$l] = $this->p->b[$l]->subtract($this->p->db[$l]->scalarMultiply($this->optimizer->getLearningRate()));
            $this->p->w[$l] = $this->p->w[$l]->subtract($this->p->dw[$l]->scalarMultiply($this->optimizer->getLearningRate()));
        }
    }
}