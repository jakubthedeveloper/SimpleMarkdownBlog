<?php

namespace MarkdownBlog\Collection;

interface CollectionInterface
{
    public function all(): iterable;
}