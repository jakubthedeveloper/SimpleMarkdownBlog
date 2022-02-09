<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\Config\PagesConfig;

interface SitemapGeneratorInterface
{
    public function generate(): void;
}