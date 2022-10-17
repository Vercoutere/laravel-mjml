<?php

namespace Vercoutere\LaravelMjml;

use Soundasleep\Html2Text;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use Vercoutere\LaravelMjml\MjmlApiClient;
use Vercoutere\LaravelMjml\Render\MjmlClient;
use Illuminate\Contracts\View\Factory as ViewFactory;

class MjmlRenderer
{
    /**
     * Create a new MJML renderer instance.
     *
     * @param  Factory  $view
     * @param  MjmlApiClient  $client
     * @return void
     */
    public function __construct(protected ViewFactory $view, protected MjmlClient $client)
    {
    }

    /**
     * Render the MJML template into HTML.
     *
     * @param  string  $view
     * @param  array  $data
     * @return \Illuminate\Support\HtmlString
     */
    public function render($view, array $data = [])
    {
        return once(function () use ($view, $data) {
            $this->view->flushFinderCache();

            return $this->renderHtml(
                $this->view->make($view, $data)->render()
            );
        });
    }

    /**
     * Render the MJML template into text.
     *
     * @param  string  $view
     * @param  array  $data
     * @return \Illuminate\Support\HtmlString
     */
    public function renderText($view, array $data = [])
    {
        return new HtmlString(html_entity_decode(
            preg_replace(
                "/[\r\n]{2,}/",
                "\n\n",
                Html2Text::convert($this->render($view, $data))
            ),
            ENT_QUOTES,
            'UTF-8',
        ));
    }

    /**
     * Render the MJML template into HTML.
     *
     * @param string $renderedView
     * @return \Illuminate\Support\HtmlString
     */
    protected function renderHtml(string $renderedView)
    {
        $filePath = $this->filePath($renderedView);

        if (!File::exists($filePath)) {
            File::put(
                $filePath,
                $this->client->render($renderedView)
            );
        }

        return new HtmlString(File::get($filePath));
    }

    /**
     * Generate a unique filepath for a rendered view.
     *
     * @param string $renderedView
     * @return string
     */
    protected function filePath(string $renderedView)
    {
        return implode([
            Str::finish(config('view.compiled'), '/'),
            hash('sha256', $renderedView),
            '.html',
        ]);
    }
}
