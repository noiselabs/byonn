<?php

namespace Noiselabs\Byonn\Initializer;

use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;

/**
 * Initializes a matrix with network parameters with all elements set to one.
 */
class Ones implements Initializer
{
    public function initialize(int $rows, int $columns): Matrix
    {
        return MatrixFactory::one($rows, $columns);
    }
}