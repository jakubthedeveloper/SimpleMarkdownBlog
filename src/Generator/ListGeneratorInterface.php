<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\DTO\PageConfigDto;

interface ListGeneratorInterface
{
    public function generate(): string;
}