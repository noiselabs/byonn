<?php

namespace Noiselabs\Byonn\CostFunction;

use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\Vector;

class MeanSquaredError implements CostFunction
{
    public function compute(Vector $prediction, Vector $target): float
    {
        $cost = 0;
        $n = $prediction->getN();

        for ($i = 0; $i < $n; $i++) {
            $cost += ($prediction[$i] - $target[$i]) ** 2;
        }

        return ($cost / $n);
    }

    public function differentiate(Matrix $prediction, Matrix $target): Matrix
    {
        return ($prediction->subtract($target))->scalarMultiply(2);
    }
}