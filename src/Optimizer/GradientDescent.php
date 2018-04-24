<?php

namespace Noiselabs\Byonn\Optimizer;

class GradientDescent implements Optimizer
{
    /**
     * @var float
     */
    private $learningRate;

    public function __construct(float $learningRate = 0.01)
    {
        $this->learningRate = $learningRate;
    }

    public function getLearningRate(): float
    {
        return $this->learningRate;
    }
}