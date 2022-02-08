<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\DTO\PageConfigDto;

interface ListGeneratorInterface
{
    public function generateShort(PageConfigDto $currentPage, int $limit): string;

    public function generate(PageConfigDto $currentPage, int $limit = null): string;
}