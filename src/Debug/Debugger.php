<?php

namespace Noiselabs\Byonn\Debug;

use MathPHP\LinearAlgebra\Vector;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Noiselabs\Byonn\Parameters;

class Debugger
{
    /**
     * @var string
     */
    private $jsonLogfilePath;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Log every `$logStep` epochs.
     *
     * @var int
     */
    private $logStep;

    public function __construct(string $jsonLogfilePath, int $logStep = 1)
    {
        $this->jsonLogfilePath = $jsonLogfilePath;
        $this->logger = $this->createLogger();
        $this->logStep = $logStep > 0 ? $logStep : 1;
    }

    public function logPreTraining(Parameters $p): void
    {
        $message = [
            'epoch' => 0,
            'example' => 0,
            'input' => null,
            'target' => null,
            'predicted' => null,
            'cost' => null,
            'parameters' => $p->export(),
        ];

        $this->logger->debug(json_encode($message));
    }

    public function logLearningStep(int $epoch, int $example, Vector $input, Vector $target, float $cost, Parameters $p): void
    {
        if (($epoch !== 1) && ($epoch % $this->logStep !== 0)) {
            return;
        }

        $layerCount = count($p->w);
        $predicted = $p->a[$layerCount];

        $message = [
            'epoch' => $epoch,
            'example' => $example,
            'input' => $input,
            'target' => $target,
            'predicted' => $predicted,
            'cost' => $cost,
            'parameters' => $p->export(),
        ];

        $this->logger->debug(json_encode($message));
    }

    private function createLogger(): Logger
    {
        $logger = new Logger('training');

        file_put_contents($this->jsonLogfilePath, '');
        $jsonStream = new StreamHandler($this->jsonLogfilePath, Logger::DEBUG);
        $jsonStream->setFormatter(new JsonFormatter());
        $logger->pushHandler($jsonStream);

        return $logger;
    }

    public function generateHtmlReport()
    {
        (new HtmlView($this->jsonLogfilePath))->generate();
    }
}