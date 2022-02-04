<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\DTO\BlogConfigDto;

interface BlogConfigParserInterface
{
    public function parse(string $yamlFilePath): BlogConfigDto;
}