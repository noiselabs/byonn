<?php

namespace Noiselabs\Byonn\CostFunction;

use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\Vector;

class CrossEntropy implements CostFunction
{
    public function compute(Vector $prediction, Vector $target): float
    {
        // cost = (-1./ m) * np.sum(np.multiply(Y, np.log(AL)) + np.multiply((1-Y), np.log( 1-AL)))

        $cost = 0;
        $n = $prediction->getN();

        for ($i = 0; $i < $n; $i++) {
            $cost += -($target[$i] * log($prediction[$i]) + (1 - $target[$i]) * log(1 - $prediction[$i]));
        }
    }

    public function differentiate(Matrix $prediction, Matrix $target): Matrix
    {
        // dAL = - (np.divide(Y, AL) - np.divide(1 - Y, 1 - AL)) # derivative of cost with respect to AL

        return - (($target / $prediction) + (1 - $target) * (1 / (1 - $prediction)));
    }
}