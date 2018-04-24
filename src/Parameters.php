<?php

namespace Noiselabs\Byonn;

use MathPHP\LinearAlgebra\Matrix;

class Parameters
{
    const BIAS = 'b';
    const WEIGHT = 'w';

    /**
     * Bias.
     *
     * @var array|Matrix[]
     */
    public $b = [];

    /**
     * Gradient of the cost with respect to `b`.
     *
     * @var array|Matrix[]
     */
    public $db = [];

    /**
     * @var array|Matrix[]
     */
    public $w = [];

    /**
     * Gradient of the cost with respect to `w`.
     *
     * @var array|Matrix[]
     */
    public $dw = [];

    /**
     * The input of the activation function.
     *
     * @var array|Matrix[]
     */
    public $z = [];

    /**
     * Backpropagation error;
     *
     * @var array|Matrix[]
     */
    public $dz = [];

    /**
     * The neuron output, after applying an activation function.
     *
     * @var array|Matrix[]
     */
    public $a = [];

    /**
     * Gradient of the cost with respect to `a`.
     *
     * @var array|Matrix[]
     */
    public $da = [];


    public function export(): array
    {
        $data = [];
        $parameters = [
            'B' => $this->b,
            'W' => $this->w,
            'Z' => $this->z,
            'A' => $this->a,
            'dA' => $this->da,
            'dZ' => $this->dz,
            'dW' => $this->dw,
            'dB' => $this->db,
        ];

        for ($l = 0; $l <= count($this->w); $l++) {
            foreach ($parameters as $k => $p) {
                /** @var $p Matrix[] */
                if (!isset($p[$l])) {
                    continue;
                }

                if ($p[$l] instanceof Matrix) {
                    $data[$k][$l] = $p[$l]->getMatrix();
                }
            }
        }

        return $data;
    }

    public function printParameters()
    {
        $this->printInputs();
        $this->printBias();
        $this->printWeights();
        $this->printPreActivations();
        $this->printActivations();
        $this->printActivationGradients();
        $this->printDeltas();
        $this->printBiasGradients();
        $this->printWeightGradients();
    }

    public function printBias()
    {
        $this->printMatrix('B', $this->b);
    }

    public function printWeights()
    {
        $this->printMatrix('W', $this->w);
    }

    public function printInputs()
    {
        if (!isset($this->a[0])) {
            return;
        }

        echo sprintf("* X\n%s\n", $this->a[0]);
    }

    public function printActivations()
    {
        $this->printMatrix('A', $this->a);
    }

    public function printPreActivations()
    {
        $this->printMatrix('Z', $this->z);
    }

    public function printDeltas()
    {
        $this->printMatrix('dZ', $this->dz);
    }

    public function printActivationGradients()
    {
        $this->printMatrix('dA', $this->da);
    }

    public function printWeightGradients()
    {
        $this->printMatrix('dW', $this->dw);
    }

    public function printBiasGradients()
    {
        $this->printMatrix('dB', $this->db);
    }

    private function printMatrix(string $varName, array $data)
    {
        foreach (array_keys($data) as $l) {
            echo sprintf("%s%d:\n%s\n", $varName, $l, $data[$l]);
        }
    }
}