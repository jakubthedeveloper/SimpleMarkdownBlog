<?php

namespace MarkdownBlog\IO;

interface FileLoaderInterface
{
    public function getFileContent(string $filePath): string;
}