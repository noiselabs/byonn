<?php

namespace Noiselabs\Byonn\Math;

use InvalidArgumentException;
use MathPHP\LinearAlgebra\Matrix;
use Webmozart\Assert\Assert;

class MatrixFunctions
{
    public static function dot(Matrix $a, Matrix $b): Matrix
    {
        return $a->multiply($b);
    }

    public static function multiply(Matrix $a, Matrix $b): Matrix
    {
        return $a->hadamardProduct($b);
    }

    public static function divide(Matrix $a, Matrix $b): Matrix
    {
        return $a->multiply($b->inverse());
    }

    public static function assertDimensions(Matrix $a, $b): void
    {
        if ($b instanceof Matrix) {
            $dims = [$b->getM(), $b->getN()];
        } elseif (!is_array($b) || count($b) !== 2) {
            throw new InvalidArgumentException(sprintf('Second parameter `$b` must be either a Matrix or an array with shape [int $m, int $n]'));
        } else {
            $dims = $b;
        }

        Assert::eq($a->getM(), $dims[0]);
        Assert::eq($a->getN(), $dims[1]);
    }
}