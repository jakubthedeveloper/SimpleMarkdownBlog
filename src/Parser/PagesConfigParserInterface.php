<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\Collection\PagesConfigCollection;

interface PagesConfigParserInterface
{
    public function parse(string $yamlFilePath): PagesConfigCollection;
}