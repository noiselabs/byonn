<?php

namespace Noiselabs\Byonn\Activation;

use MathPHP\LinearAlgebra\Matrix;

interface Activation
{
    public function compute(Matrix $values) : Matrix;

    public function differentiate(Matrix $values): Matrix;
}
