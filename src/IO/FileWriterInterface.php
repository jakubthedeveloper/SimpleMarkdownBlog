<?php

namespace MarkdownBlog\IO;

interface FileWriterInterface
{
    public function saveFile(string $filePath, string $content): string;
}