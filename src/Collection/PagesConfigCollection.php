<?php

namespace MarkdownBlog\Collection;

use MarkdownBlog\DTO\PageConfigDto;

class PagesConfigCollection implements CollectionInterface
{
    /**
     * @var array|PageConfigDto[]
     */
    private array $configs = [];

    /**
     * @return iterable|PageConfigDto[]
     */
    public function all(): iterable
    {
        return $this->configs;
    }

    public function add(PageConfigDto $config)
    {
        $this->configs[] = $config;
    }
}