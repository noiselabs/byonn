<?php

namespace Noiselabs\Byonn\Optimizer;

interface Optimizer
{
    public function getLearningRate(): float;
}