<?php

namespace MarkdownBlog\IO;

use MarkdownBlog\Exception\CantOpenFile;

class FileWriter implements FileWriterInterface
{
    public function saveFile(string $filePath, string $content): string
    {
        file_put_contents(
            $filePath,
            $content
        );
    }
}