<?php

namespace Vercoutere\LaravelMjml\Render;

interface MjmlClient
{
    public function render(string $mjml): string;
}
