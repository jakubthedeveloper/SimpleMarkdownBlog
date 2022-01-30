<?php

namespace MarkdownBlog\IO;

use MarkdownBlog\Exception\CantOpenFile;

class FileLoader implements FileLoaderInterface
{
    public function getFileContent(string $filePath): string
    {
        if (false === file_exists($filePath)) {
            throw new CantOpenFile(
                sprintf("File %s does not exist.", $filePath)
            );
        }

        return file_get_contents($filePath);
    }
}