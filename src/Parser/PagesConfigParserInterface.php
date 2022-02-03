<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\Collection\PagesConfigCollection;

interface PagesConfigParserInterface
{
    /**
     * @return PagesConfigCollection
     */
    public function parse(string $yamlFilePath): PagesConfigCollection;
}