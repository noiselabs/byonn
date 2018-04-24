<?php

namespace Noiselabs\Byonn\Activation;

use MathPHP\LinearAlgebra\Matrix;

class Identity implements Activation
{
    public function compute(Matrix $values) : Matrix
    {
        return $values;
    }

    public function differentiate(Matrix $values) : Matrix
    {
        return $values;
    }
}