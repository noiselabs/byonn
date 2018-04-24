<?php

namespace Noiselabs\Byonn\Activation;

use MathPHP\LinearAlgebra\Matrix;

class Tanh implements Activation
{
    /**
     * @var float
     */
    private $beta;

    public function __construct(float $beta = 1.0)
    {
        $this->beta = $beta;
    }

    public function compute(Matrix $values) : Matrix
    {
        return $values->map(function($value) {
            tanh($this->beta * $value);
        });
    }

    public function differentiate(Matrix $values) : Matrix
    {
        return $values->map(function($value) {
            return 1.0 - $value ** 2;
        });
    }
}
