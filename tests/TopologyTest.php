<?php

namespace Noiselabs\Byonn\Test;

use Noiselabs\Byonn\Activation\Identity;
use Noiselabs\Byonn\Activation\LeakyReLU;
use Noiselabs\Byonn\Activation\Sigmoid;
use Noiselabs\Byonn\Topology;
use PHPUnit\Framework\TestCase;

class TopologyTest extends TestCase
{
    public function test_with_no_hidden_layers()
    {
        $topology = new Topology([2, 1], [new Sigmoid()]);

        self::assertEquals(1, $topology->layerCount());
        self::assertEquals(2, $topology->inputs());
        self::assertEquals(1, $topology->outputs());

        $activations = $topology->getActivations();
        self::assertCount(1, $activations);
        self::assertInstanceOf(Sigmoid::class, $activations[1]);
    }

    public function test_with_multiple_hidden_layer()
    {
        $topology = new Topology([2, 3, 5, 3, 2], [new LeakyReLU(), new Sigmoid()]);

        self::assertEquals(4, $topology->layerCount());
        self::assertEquals(2, $topology->inputs());
        self::assertEquals(2, $topology->outputs());
        self::assertEquals([3, 5, 3], $topology->hiddenLayers());

        foreach ([2, 3, 5, 3, 2] as $i => $nodeCount) {
            self::assertEquals($nodeCount, $topology->neuronsCount($i));
        }

        $activations = $topology->getActivations();
        self::assertCount(4, $activations);
        self::assertInstanceOf(LeakyReLU::class, $activations[1]);
        self::assertInstanceOf(LeakyReLU::class, $activations[2]);
        self::assertInstanceOf(LeakyReLU::class, $activations[3]);
        self::assertInstanceOf(Sigmoid::class, $activations[4]);
    }

    public function test_with_a_single_choice_of_activation_function()
    {
        $topology = new Topology([2, 3, 5, 3, 2], [new Sigmoid()]);

        $activations = $topology->getActivations();
        self::assertCount(4, $activations);
    }

    public function test_it_fills_with_activation_functions()
    {
        $topology = new Topology([2, 3, 5, 3, 2], [new LeakyReLU(), new Sigmoid(), new Identity()]);

        $activations = $topology->getActivations();
        self::assertCount(4, $activations);
        self::assertInstanceOf(LeakyReLU::class, $activations[1]);
        self::assertInstanceOf(Sigmoid::class, $activations[2]);
        self::assertInstanceOf(Sigmoid::class, $activations[3]);
        self::assertInstanceOf(Identity::class, $activations[4]);
    }
}
