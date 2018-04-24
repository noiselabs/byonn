<?php

namespace Noiselabs\Byonn\Initializer;

use MathPHP\LinearAlgebra\Matrix;

interface Initializer
{
    public function initialize(int $rows, int $columns): Matrix;
}