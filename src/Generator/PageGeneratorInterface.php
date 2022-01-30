<?php

namespace MarkdownBlog\Generator;

interface PageGeneratorInterface
{
    public function generate(string $markdownFile, string $outputFile): void;
}