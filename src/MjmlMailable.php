<?php

namespace Vercoutere\LaravelMjml;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Vercoutere\LaravelMjml\MjmlRenderer;

class MjmlMailable extends Mailable
{
    /**
     * The MJML template for the message (if applicable).
     *
     * @var string
     */
    public $mjml;

    /**
     * Set the MJML template for the message.
     *
     * @param  string  $view
     * @param  array  $data
     * @return $this
     */
    public function mjml($view, array $data = [])
    {
        $this->mjml = $view;
        $this->viewData = array_merge($this->viewData, $data);

        return $this;
    }

    /**
     * Build the view for the message.
     *
     * @return array|string
     *
     * @throws \ReflectionException
     */
    protected function buildView()
    {
        if (isset($this->mjml)) {
            return $this->buildMjmlView();
        }

        return parent::buildView();
    }

    /**
     * Build the MJML view for the message.
     *
     * @return array
     *
     * @throws \ReflectionException
     */
    protected function buildMjmlView()
    {
        $mjml = App::make(MjmlRenderer::class);
        $data = $this->buildViewData();

        return [
            'html' => $mjml->render($this->mjml, $data),
            'text' => $this->textView ?? $mjml->renderText($this->mjml, $data),
        ];
    }
}
