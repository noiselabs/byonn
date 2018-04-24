<?php

namespace Noiselabs\Byonn;

use Noiselabs\Byonn\Activation\Activation;
use Webmozart\Assert\Assert;

class Topology
{
    /**
     * @var array|int[]
     */
    private $layers;

    /**
     * @var array|Activation[]
     */
    private $activations;

    /**
     * Topology constructor.
     *
     * @param int ...$layers
     */
    public function __construct(array $layers, array $activations)
    {
        $this->layers = [];
        foreach ($layers as $layer) {
            Assert::integer($layer);
            $this->layers[] = $layer;
        }

        $this->activations = $this->assignActivationsToLayers($activations);
    }

    /**
     * Number of neurons in the input "layer".
     *
     * @return int
     */
    public function inputs(): int
    {
        return $this->layers[0];
    }

    /**
     * Array with hidden layers, each element defining the number of neurons in that layer.
     *
     * @return array
     */
    public function hiddenLayers(): array
    {
        return array_slice($this->layers, 1, $this->layerCount() - 1);
    }

    /**
     * Number of neurons in the output layer.
     *
     * @return int
     */
    public function outputs(): int
    {
        return $this->layers[$this->layerCount()];
    }

    /**
     * Total number of layers, excluding the input "layer".
     *
     * @return int
     */
    public function layerCount(): int
    {
        return count($this->layers) - 1;
    }

    /**
     * Number of neurons in the layer $layer, starting at 1 for the input "layer".
     *
     * @param int $layer
     *
     * @return int
     */
    public function neuronsCount(int $layer): int
    {
        if (!isset($this->layers[$layer])) {
            return 0;
        }

        return $this->layers[$layer];
    }

    /**
     * @return array|Activation[]
     */
    public function getActivations(): array
    {
        return $this->activations;
    }

    private function normalizeKeys(array $activations): array
    {
        $activations = array_values($activations);
        array_unshift($activations, null);
        unset($activations[0]);

        return $activations;
    }

    private function assignActivationsToLayers(array $activations): array
    {
        $layerCount = $this->layerCount();

        Assert::lessThanEq(count($activations), $layerCount);

        if ($layerCount == 1 || count($activations) == $layerCount) {
            return $this->normalizeKeys($activations);
        }

        if (count($activations) == 1) {
            return array_fill(1, $layerCount, reset($activations));
        }

        $a[$layerCount] = array_pop($activations);
        $filler = end($activations);

        $a = array_merge(
            array_slice($activations, 0, count($activations) - 1),
            array_fill(count($activations), $layerCount - count($activations), $filler),
            $a
        );

        return $this->normalizeKeys($a);
    }
}