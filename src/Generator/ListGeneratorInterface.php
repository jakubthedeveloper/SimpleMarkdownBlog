<?php

namespace MarkdownBlog\Generator;

interface ListGeneratorInterface
{
    public function generate(): string;
}