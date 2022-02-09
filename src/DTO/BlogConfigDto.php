<?php

namespace MarkdownBlog\DTO;

use JetBrains\PhpStorm\Pure;

class BlogConfigDto
{
    public function __construct(
        public readonly string $title,
        public readonly string $baseUrl,
        public readonly string $footerText,
        public readonly int $shortPagesListItemsCount
    ) {

    }
}