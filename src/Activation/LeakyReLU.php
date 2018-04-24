<?php

namespace Noiselabs\Byonn\Activation;

use MathPHP\LinearAlgebra\Matrix;

class LeakyReLU implements Activation
{
    /**
     * @var float
     */
    private $alpha;

    public function __construct(float $alpha = 0.01)
    {
        $this->alpha = $alpha;
    }

    public function compute(Matrix $values) : Matrix
    {
        return $values->map(function($value) {
            return $value >= 0 ? $value : $this->alpha * $value;
        });
    }

    public function differentiate(Matrix $values) : Matrix
    {
        return $values->map(function($value) {
            return $value >= 0 ? 1 : $this->alpha;
        });
    }
}
