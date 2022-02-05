<?php

namespace MarkdownBlog\Config;

use MarkdownBlog\DTO\BlogConfigDto;
use MarkdownBlog\Parser\BlogConfigParserInterface;

class BlogConfig implements BlogConfigInterface
{
    private static ?BlogConfigDto $blogConfig = null;

    public function __construct(
        private string $configDir,
        private BlogConfigParserInterface $blogConfigParser
    ) {

    }

    public function getTitle(): string
    {
        return $this->getConfig()->title;
    }

    public function getFooterText(): string
    {
        return $this->getConfig()->footerText;
    }

    public function getShortPagesListItemsCount(): int
    {
        return $this->getConfig()->shortPagesListItemsCount;
    }

    private function getConfig(): BlogConfigDto
    {
        if (null === self::$blogConfig) {
            self::$blogConfig = $this->blogConfigParser->parse($this->configDir . '/blog.yaml');
        }

        return self::$blogConfig;
    }
}