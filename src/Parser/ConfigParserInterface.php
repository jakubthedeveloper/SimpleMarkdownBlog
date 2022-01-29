<?php

namespace MarkdownBlog\Parser;

interface ConfigParserInterface
{
    public function parse(string $yamlContents): array;
}