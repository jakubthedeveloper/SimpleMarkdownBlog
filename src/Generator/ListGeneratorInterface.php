<?php

namespace MarkdownBlog\Generator;

interface ListGeneratorInterface
{
    public function generateShort(int $limit): string;

    public function generate(?int $limit = null): string;
}