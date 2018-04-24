<?php

namespace Noiselabs\Byonn\Activation;

use MathPHP\Functions\Special;
use MathPHP\LinearAlgebra\Matrix;

class Sigmoid implements Activation
{
    public function compute(Matrix $values): Matrix
    {
        return $values->map(function($value) {
            return Special::sigmoid($value);
        });
    }

    public function differentiate(Matrix $values): Matrix
    {
        return $values->map(function($value) {
            $computedValue = Special::sigmoid($value);

            return $computedValue * (1 - $computedValue);
        });
    }
}