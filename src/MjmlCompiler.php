<?php

namespace Vercoutere\LaravelMjml;

use Illuminate\View\Compilers\BladeCompiler;
use Vercoutere\LaravelMjml\Render\MjmlClient;

class MjmlCompiler extends BladeCompiler
{
    /**
     * @var \Vercoutere\LaravelMjml\Render\MjmlClient
     */
    protected $mjmlClient;

    /**
     * Set the MJML client instance.
     *
     * @param \Vercoutere\LaravelMjml\Render\MjmlClient $client
     * @return \Vercoutere\LaravelMjml\MjmlCompiler
     */
    public function setClient(MjmlClient $client)
    {
        $this->mjmlClient = $client;
        return $this;
    }

    /**
     * Compile the given Blade/MJML template contents.
     *
     * @param  string  $value
     * @return string
     */
    public function compileString($value)
    {
        return $this->mjmlClient->render(parent::compileString($value));
    }
}
