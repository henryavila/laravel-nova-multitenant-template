<?php

declare(strict_types=1);

namespace App\Interfaces;

interface FileStoredInDb
{
    /**
     * Get the file mime type
     */
    public function getMime(): ?string;

    /**
     * Get the name of the file WITH THE EXTENSION
     */
    public function getFileName(): ?string;

    /**
     * Magic Method to file_url attribute containing the URL to open/download the file
     */
    public function getFileUrlAttribute(): ?string;

    /**
     * Determine if the model has a file stored in DB
     */
    public function hasFile(): bool;

    /**
     * Get the File in RAW format as string
     */
    public function getFileData(): ?string;
}
