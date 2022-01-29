<?php

namespace MarkdownBlog\Transformer;

class MarkdownToHtml implements MarkdownToHtmlInterface
{
    private \Parsedown $parseDown;

    public function __construct()
    {
        $this->parseDown = new \Parsedown();
    }

    public function transformText(string $yamlContents): string
    {
        return $this->parseDown->text($yamlContents);
    }
}