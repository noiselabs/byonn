<?php

namespace Noiselabs\Byonn\Initializer;

use MathPHP\LinearAlgebra\Matrix;

class ParametersInitializer
{
    /**
     * @var Initializer
     */
    private $biasesInitializer;
    /**
     * @var Initializer
     */
    private $weightsInitializer;

    public function __construct(Initializer $biasesInitializer, Initializer $weightsInitializer)
    {
        $this->biasesInitializer = $biasesInitializer;
        $this->weightsInitializer = $weightsInitializer;
    }

    public function initializeBiases(int $rows, int $columns): Matrix
    {
        return $this->biasesInitializer->initialize($rows, $columns);
    }

    public function initializeWeights(int $rows, int $columns): Matrix
    {
        return $this->weightsInitializer->initialize($rows, $columns);
    }
}