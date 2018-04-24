<?php

namespace Noiselabs\Byonn\CostFunction;

use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\Vector;

interface CostFunction
{
    public function compute(Vector $prediction, Vector $target): float;

    public function differentiate(Matrix $prediction, Matrix $target): Matrix;
}