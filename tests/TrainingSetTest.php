<?php

namespace Noiselabs\ByonnTest;

use MathPHP\LinearAlgebra\Vector;
use Noiselabs\Byonn\TrainingSet;
use PHPUnit\Framework\TestCase;

class TrainingSetTest extends TestCase
{
    public function test_it_converts_inputs_and_targets_to_vectors()
    {
        $inputs = [
            [0, 0], // 0
            [0, 1], // 1
            [1, 0], // 1
            [1, 1], // 0
        ];
        $targets = [0, 1, 1, 0];

        $dataset = new TrainingSet($inputs, $targets);

        self::assertInternalType('array', $dataset->getInputs());
        self::assertInternalType('array', $dataset->getTargets());

        foreach ($dataset->getInputs() as $input) {
            self::assertInstanceOf(Vector::class, $input);
        }

        foreach ($dataset->getTargets() as $target) {
            self::assertInstanceOf(Vector::class, $target);
        }
    }
}
