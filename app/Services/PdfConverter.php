<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot;

/**
 * A Wrapper to puppeteer (headless chrome in NodeJs) USING https://github.com/spatie/browsershot
 *
 * possible combination for Google Chart: https://quickchart.io/documentation/google-charts-image-server/
 */
class PdfConverter
{
    protected Browsershot $handler;

    protected ?array $errors = [];

    /**
     * Must be called AFTER Browsershot::html or Browsershot::url
     */
    public function setDefaultOptions($printAsScreen = true): Browsershot
    {
        foreach (config('pdf-converter.report.chrome-options') as $key => $option) {
            $this->handler->setOption($key, $option);
        }

        $this->handler->addChromiumArguments(config('pdf-converter.report.chrome-arguments'));
        $this->handler->timeout(360);

        $this->handler->waitUntilNetworkIdle();
        if ($printAsScreen) {
            $this->handler->emulateMedia('screen'); // "screen", "print" (default) or null (passing null disables the emulation).
        }

        //$this->handler->setDelay(2000); // Not required

        return $this->handler;
    }

    public function landscape(): self
    {
        $this->handler->landscape();

        return $this;
    }

    /**
     * Load a view file as the html to be converted into a pdf.
     */
    public function loadView(string $view, array $data = [], array $mergeData = []): PdfConverter
    {
        $view = View::make($view, $data, $mergeData);
        $this->handler = Browsershot::html($view->render());

        // Will use the $this->handler saved by Browsershot::html($view);
        $this->setDefaultOptions();

        return $this;
    }

    public function hideBackground(): static
    {
        $this->handler->hideBackground();

        return $this;
    }

    public function showBackground(): static
    {
        $this->handler->showBackground();

        return $this;
    }

    /**
     * Save the file once done converting.
     */
    public function save(string $targetPath): Browsershot
    {
        try {
            $this->handler->save($targetPath);
        } catch (CouldNotTakeBrowsershot $e) {
            throw new Exception('Não foi possível inicar a conversão para PDF');
        }

        $this->errors = $this->handler->consoleMessages();
        $this->logErros($targetPath);

        return $this->handler;
    }

    /**
     * @throws Exception
     */
    public function saveInTemp(string $fileName = null): string
    {
        $fileName = $fileName ?? Str::uuid()->toString().Str::random(32).'.pdf';
        $targetPath = tempnam(sys_get_temp_dir(), $fileName);
        $this->save($targetPath);

        return $fileName;
    }

    /**
     * Display the file within the browser itself.
     */
    public function inline(string $fileName = 'file.pdf')
    {
        $this->embed($this->raw(), $fileName);
    }

    public function raw(): string
    {
        return $this->handler->pdf();
    }

    /**
     * Force the browser to embed the PDF file.
     *
     * @throws Exception
     */
    public function embed(string $pdfContent, string $fileName = 'arquivo.pdf', bool $exit = true)
    {
        if (headers_sent()) {
            throw new Exception('Headers have already been sent');
        }

        if (empty($fileName)) {
            throw new Exception('Please specify a valid filename');
        }

        header('Content-type: application/pdf');
        header('Cache-control: public, must-revalidate, max-age=0');
        header('Pragme: public');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d m Y H:i:s').' GMT');
        header('Content-Length: '.strlen($pdfContent));
        header('Content-Disposition: inline; filename="'.basename($fileName).'";');

        echo $pdfContent;

        if ($exit === true) {
            exit;
        }
    }

    protected function logErros($fileName): void
    {
        if (! empty($this->errors)) {
            Log::error("Pdf error - {$fileName}", $this->errors);
        }
    }
}
