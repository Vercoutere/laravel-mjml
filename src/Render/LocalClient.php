<?php

namespace Vercoutere\LaravelMjml\Render;

use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class LocalClient implements MjmlClient
{
    public function __construct(protected string $binaryPath, protected string $nodePath)
    {
    }

    public function render(string $mjml): string
    {
        $process = new Process([
            $this->nodePath,
            $this->binaryPath,
            '-i',
            '--config.minify',
            'true',
            '-s',
            '--noStdoutFileComment',
        ]);

        $process->setInput($mjml);
        $process->mustRun();

        return $process->getOutput();
    }

    protected function compiledFilePath(string $content)
    {
        return Str::finish(config('view.compiled'), '/') . hash('sha256', $content);
    }
}
