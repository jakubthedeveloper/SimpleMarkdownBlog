<?php

namespace MarkdownBlog\Config;

use MarkdownBlog\Collection\PagesConfigCollection;

interface BlogConfigInterface
{
    public function getTitle(): string;

    public function getFooterText(): string;

    public function getShortPagesListItemsCount(): int;

}