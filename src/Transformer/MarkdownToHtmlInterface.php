<?php

namespace MarkdownBlog\Transformer;

interface MarkdownToHtmlInterface
{
    public function transformText(string $yamlContents): string;
}