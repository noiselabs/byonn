<?php

namespace Noiselabs\ByonnTest\Math;

use MathPHP\LinearAlgebra\Matrix;
use Noiselabs\Byonn\Math\MatrixFunctions as M;
use PHPUnit\Framework\TestCase;

class MatrixFunctionsTest extends TestCase
{
    public function test_it_calculates_dot_operation()
    {
        $a = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);

        $b = new Matrix([
            [7, 8],
            [9, 10],
            [11, 12],
        ]);

        $expected = new Matrix([
            [58, 64],
            [139, 154],
        ]);

        $c = M::dot($a, $b);

        self::assertEquals($expected->getMatrix(), $c->getMatrix());
    }

    public function test_it_calculates_multiply_operation()
    {
        $a = new Matrix([
            [1, 2],
            [4, 5],
        ]);

        $b = new Matrix([
            [7, 8],
            [9, 10],
        ]);

        $expected = new Matrix([
            [7, 16],
            [36, 50],
        ]);

        $c = M::multiply($a, $b);

        self::assertEquals($expected->getMatrix(), $c->getMatrix());
    }
}
