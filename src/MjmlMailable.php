<?php

namespace Vercoutere\LaravelMjml;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\App;
use Vercoutere\LaravelMjml\MjmlRenderer;

class MjmlMailable extends Mailable
{
    /**
     * Build the view for the message.
     *
     * @return array|string
     *
     * @throws \ReflectionException
     */
    protected function buildView()
    {
        if (isset($this->view)) {
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
            'html' => $mjml->render($this->view, $data),
            'text' => $this->textView ?? $mjml->renderText($this->view, $data),
        ];
    }
}
