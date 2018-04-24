<?php

namespace Noiselabs\Byonn;

use MathPHP\LinearAlgebra\Vector;
use Webmozart\Assert\Assert;

/**
 * A dataset used to learn containing a list of training examples.
 */
class TrainingSet
{
    /**
     * @var array|Vector[]
     */
    private $inputs;

    /**
     * @var array|Vector[]
     */
    private $targets;

    /**
     * @var int
     */
    private $size;

    public function __construct(array $inputs, array $targets)
    {
        Assert::eq(count($inputs), count($targets),
            sprintf('Number of input samples and targets mismatch: %d vs %d', count($inputs), count($inputs)));

        $this->size = count($inputs);
        $this->inputs = [];
        $this->targets = [];

        // Make sure both input samples and targets are arrays because sometimes only one element is provided
        for ($i = 0; $i < $this->size; $i++) {
            $this->inputs[] = new Vector(is_array($inputs[$i]) ? array_values($inputs[$i]) : [$inputs[$i]]);
            $this->targets[] = new Vector(is_array($targets[$i]) ? array_values($targets[$i]) : [$targets[$i]]);
        }
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return array|Vector[]
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return array|Vector[]
     */
    public function getTargets(): array
    {
        return $this->targets;
    }
}