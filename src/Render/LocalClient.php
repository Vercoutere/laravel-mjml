<?php

namespace Vercoutere\LaravelMjml\Render;

use Symfony\Component\Process\Process;

class LocalClient implements MjmlClient
{
    public function __construct(protected string $binaryPath)
    {
    }

    public function render(string $mjml): string
    {
        return $this->getProcess()
            ->setInput($mjml)
            ->mustRun()
            ->getOutput();
    }

    protected function getProcess(): Process
    {
        return new Process([
            $this->binaryPath,
            '-i',
            '--config.minify',
            'true',
            '-s',
            '--noStdoutFileComment',
        ]);
    }
}
