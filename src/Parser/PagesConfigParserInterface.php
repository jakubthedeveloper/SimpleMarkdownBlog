<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\DTO\PageConfigDto;

interface PagesConfigParserInterface
{
    /**
     * @return iterable|PageConfigDto[]
     */
    public function parse(string $yamlFilePath): iterable;
}