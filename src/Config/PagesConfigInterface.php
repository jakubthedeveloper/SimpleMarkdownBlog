<?php

namespace MarkdownBlog\Config;

use MarkdownBlog\Collection\PagesConfigCollection;

interface PagesConfigInterface
{
    public function getPagesConfig(): PagesConfigCollection;

}