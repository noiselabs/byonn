<?php

namespace Noiselabs\Byonn\Initializer;

use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\Probability\Distribution\Continuous\Uniform as UniformDistribution;

/**
 * Initialization with random values from a uniform distribution.
 */
class RandomUniform implements Initializer
{
    /**
     * @var UniformDistribution
     */
    private $randomNumberGenerator;

    public function __construct(float $minValue, float $maxValue)
    {
        $this->randomNumberGenerator = new UniformDistribution($minValue, $maxValue);
    }

    public function initialize(int $rows, int $columns): Matrix
    {
        return MatrixFactory::one($rows, $columns)->map(function($v) {
            return $this->randomNumberGenerator->rand();
        });
    }
}