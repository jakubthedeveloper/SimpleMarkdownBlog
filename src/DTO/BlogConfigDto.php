<?php

namespace MarkdownBlog\DTO;

use JetBrains\PhpStorm\Pure;

class BlogConfigDto
{
    public function __construct(
        public readonly string $title
    ) {

    }
}