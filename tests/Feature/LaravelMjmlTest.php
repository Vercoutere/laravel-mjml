<?php

namespace Vercoutere\LaravelMjml\Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\Test;
use Vercoutere\LaravelMjml\Render\LocalClient;
use Orchestra\Testbench\Attributes\DefineEnvironment;

class LaravelMjmlTest extends \Orchestra\Testbench\TestCase
{
    const HTML = '<html><head></head><body>some html</body></html>';

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            \Vercoutere\LaravelMjml\MjmlServiceProvider::class,
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            View::addLocation(realpath(__DIR__ . '/../Fixtures/views'));
            config()->set('view.cache', false);
        });

        parent::setUp();
    }

    protected function usesLocalStrategy($app)
    {
        $app['config']->set('mjml.strategy', 'local');
    }

    protected function usesApiStrategy($app)
    {
        $app['config']->set('mjml.strategy', 'api');
        $app['config']->set('mjml.api_credentials.application_id', 'abc');
        $app['config']->set('mjml.api_credentials.secret_key', '123');
    }

    #[Test]
    #[DefineEnvironment('usesLocalStrategy')]
    public function it_renders_mjml_using_the_local_strategy()
    {
        $process = Mockery::mock(\Symfony\Component\Process\Process::class);

        $this->partialMock(
            LocalClient::class,
            function (MockInterface $mock) use ($process) {
                $mock->shouldAllowMockingProtectedMethods();
                $mock->allows()->getProcess()->andReturn($process);
            }
        );

        $process
            ->expects()
            ->setInput("<mjml><mj-body><?php echo e(config('app.name')); ?></mj-body></mjml>\n")
            ->andReturnSelf();

        $process
            ->expects()
            ->mustRun()
            ->andReturnSelf();

        $process
            ->expects()
            ->getOutput()
            ->andReturn(self::HTML);

        (new Mailable())
            ->assertSeeInText('some html')
            ->assertSeeInHtml(self::HTML, false);
    }

    #[Test]
    #[DefineEnvironment('usesApiStrategy')]
    public function it_renders_mjml_using_the_api_strategy()
    {
        Http::fake(['*' => Http::response(['html' => self::HTML])]);

        (new Mailable())
            ->assertSeeInText('some html')
            ->assertSeeInHtml(self::HTML, false);

        Http::assertSent(function (Request $request) {
            return $request->method() == 'POST' &&
            $request->url() == 'https://api.mjml.io/v1/render' &&
            $request['mjml'] == "<mjml><mj-body><?php echo e(config('app.name')); ?></mj-body></mjml>\n";
        });
    }
}

class Mailable extends \Vercoutere\LaravelMjml\MjmlMailable
{
    public function content(): \Illuminate\Mail\Mailables\Content
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'test-mail',
        );
    }
}
