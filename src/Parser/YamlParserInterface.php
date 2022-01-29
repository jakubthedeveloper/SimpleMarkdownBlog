<?php

namespace MarkdownBlog\Parser;

interface YamlParserInterface
{
    public function parse(string $yamlContents): array;
}