<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\FileStoredInDb;
use Illuminate\Http\Response;

/**
 * Manipulate file stored in DB
 */
class FileFromDbService
{
    public function __construct(public FileStoredInDb $model)
    {
    }

    /**
     * Return a response with the file
     */
    public function servePdfFile(): Response
    {
        return $this->serveFile('application/pdf');
    }

    public function serveFile(string $mimeType = null): Response
    {
        $data = $this->model->getFileData();
        if (empty($data)) {
            abort(404);
        }

        return response(
            content: $data,
            status: 200,
            headers: [
                'Content-type' => $mimeType ?? $this->model->getMime(),
                'Pragma' => 'no-cache',
                'Cache-control' => 'public, must-revalidate, max-age=0',
                'Pragme' => 'public',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'Last-Modified' => ''.gmdate('D, d m Y H:i:s').' GMT',
                'Content-Disposition' => "inline; filename=\"{$this->model->getFileName()}\";",
                'Content-length' => strlen($data),
            ]
        );
    }
}
