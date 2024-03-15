<?php

namespace Vercoutere\LaravelMjml\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Foundation\Console\ViewCacheCommand as BaseViewCacheCommand;

class ViewCacheCommand extends BaseViewCacheCommand
{
    /**
     * Compile the given view files.
     *
     * @param  \Illuminate\Support\Collection  $views
     * @return void
     */
    protected function compileViews(Collection $views)
    {
        $bladeCompiler = $this->laravel['view']->getEngineResolver()->resolve('blade')->getCompiler();

        $mjmlCompiler = $this->laravel['view']->getEngineResolver()->resolve('mjml')->getCompiler();

        $views->map(function (SplFileInfo $file) use ($bladeCompiler, $mjmlCompiler) {
            $this->components->task('    '.$file->getRelativePathname(), null, OutputInterface::VERBOSITY_VERY_VERBOSE);

            str_contains($file->getBasename(), '.mjml') ?
                $mjmlCompiler->compile($file->getRealPath()) :
                $bladeCompiler->compile($file->getRealPath());
        });

        if ($this->output->isVeryVerbose()) {
            $this->newLine();
        }
    }
}
