<?php

namespace MarkdownBlog\Config;

use MarkdownBlog\Collection\PagesConfigCollection;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Parser\PagesConfigParserInterface;

class PagesConfig implements PagesConfigInterface
{
    private static ?PagesConfigCollection $pagesConfig = null;

    public function __construct(
        private string $configDir,
        private PagesConfigParserInterface $pagesConfigParser
    ) {

    }

    /**
     * @return PagesConfigCollection
     */
    public function getPagesConfig(): PagesConfigCollection
    {
        if (null === self::$pagesConfig) {
            self::$pagesConfig = $this->pagesConfigParser->parse($this->configDir . '/pages.yaml');
        }

        return self::$pagesConfig;
    }
}