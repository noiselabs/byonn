<?php

namespace Noiselabs\Byonn\Debug;

use RuntimeException;

class HtmlView
{
    const THEME_BLUE = 'primary';
    const THEME_GREY = 'secondary';
    const THEME_GREEN = 'success';
    const THEME_RED = 'danger';
    const THEME_YELLOW = 'warning';
    const THEME_TEAL = 'info';
    const THEME_WHITE = 'light';
    const THEME_BLACK = 'dark';

    /**
     * @var string
     */
    private $jsonLogfilePath;

    /**
     * @var string
     */
    private $outputFilePath;

    /**
     * @var string
     */
    private $theme;

    public function __construct(string $jsonLogfilePath, string $theme = self::THEME_YELLOW)
    {
        $this->jsonLogfilePath = $jsonLogfilePath;
        $this->outputFilePath = substr_replace($jsonLogfilePath , 'html', strrpos($jsonLogfilePath , '.') +1);
        $this->theme = $theme;
    }

    public function generate(): void
    {
        if (!is_readable($this->jsonLogfilePath)) {
            throw new RuntimeException(sprintf('File "%s" isn\'t readable', $this->jsonLogfilePath));
        }

        $content = '';

        $thead = <<<'EOT'
        <tr>
            <th scope="col">Epoch (example)</th>
            <th scope="col">Input</th>
            <th scope="col">Target</th>
            <th scope="col">Predicted</th>
            <th scope="col">Cost</th>
            <th scope="col">Bias <var>(B)</var></th>
            <th scope="col">Bias gradients <var>(dB)</var></th>
            <th scope="col">Weights <var>(W)</var></th>
            <th scope="col">Weights gradients <var>(dW)</var></th>
            <th scope="col">Neuron input <var>(Z)</var></th>
            <th scope="col">Neuron input gradients <var>(dZ)</var></th>
            <th scope="col">Activations <var>(A)</var></th>
            <th scope="col">Activations gradients <var>(dA)</var></th>
        </tr>
EOT;

        $handle = fopen($this->jsonLogfilePath, 'r');
        if ($handle) {
            $r = 1;
            while (($line = fgets($handle)) !== false) {
                if ($r % 10 == 0) {
                    $content .= $thead;
                }

                $data = json_decode($line, true);
                $content .= $this->renderStep(json_decode($data['message'], true));
                $r++;
            }

            fclose($handle);
        } else {
            throw new RuntimeException(sprintf('File "%s" could not be open for reading', $this->jsonLogfilePath));
        }

        $html = file_get_contents(__DIR__ . '/resources/default.html');
        $html = str_replace(['{{ thead }}', '{{ content }}', '{{ theme }}'], [$thead, $content, $this->theme], $html);
        file_put_contents($this->outputFilePath, $html);
    }

    private function renderStep(array $data): string
    {
        $content = sprintf('<tr%s>', $data['epoch'] % 2 == 0 ? ' class="tr-stripped"' : '');
        $content .= '<th scope="row"><samp>' . $data['epoch'] .  ' ('. $data['example'] .')</samp></th>';
        $content .= '<td><pre>' . $this->renderArray($data['input']) . '</pre></td>';
        $content .= '<td><pre>' . $this->renderArray($data['target']) . '</pre></td>';
        $content .= '<td><pre>' . $this->renderArray($data['predicted'][0]) . '</pre></td>';
        $content .= '<td><pre>' . ($data['cost'] ?: 'N/A') . '</pre></td>';
        foreach (['B', 'dB', 'W', 'dW', 'Z', 'dZ', 'A', 'dA'] as $p) {
            if (!isset($data['parameters'][$p])) {
                $content .= '<td><pre>N/A</pre></td>';
                continue;
            }
            $content .= '<td><pre>' . $this->renderParameter($data['parameters'][$p]) . '</pre></td>';
        }
        $content .= '</tr>';

        return $content;
    }

    private function renderArray($data): string
    {
        if (!is_array($data)) {
            return 'N/A';
        }

        return '[' . implode(', ', $data) . ']';
    }

    private function renderParameter($data): string
    {
        if (!is_array($data)) {
            return 'N/A';
        }

        $content = '';
        foreach (array_keys($data) as $l) {
            $content .= sprintf('L%d: [', $l);
            foreach (array_keys($data[$l]) as $n) {
                $content .= $this->renderArray($data[$l][$n]);
            }
            $content .= ']';
            $content .= '</br>';
        }

        return $content;
    }
}