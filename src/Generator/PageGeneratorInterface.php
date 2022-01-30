<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\DTO\PageConfigDto;

interface PageGeneratorInterface
{
    public function generate(PageConfigDto $page): void;
}