<?php

declare(strict_types=1);

namespace MarkdownBlog\Config;

use MarkdownBlog\DTO\BlogConfigDto;
use MarkdownBlog\IO\FileLoader;
use MarkdownBlog\Parser\BlogConfigParserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @property BlogConfigParserInterface|\PHPUnit\Framework\MockObject\MockObject $blogConfigParser
 * @property BlogConfig $blogConfig
 */
class BlogConfigParserTest extends TestCase
{
    public function setUp(): void
    {
        $this->blogConfigParser = $this->createMock(BlogConfigParserInterface::class);

        $this->blogConfig = new BlogConfig(
            configDir: 'config/',
            blogConfigParser: $this->blogConfigParser
        );
    }

    public function testBlogConfiguration(): void
    {
        $this->blogConfigParser->expects($this->once())
            ->method('parse')
            ->willReturn(
                new BlogConfigDto(
                    title: "Test blog title",
                    footerText: "Test blog footer",
                    shortPagesListItemsCount: 10
                )
            );

        $this->assertEquals("Test blog title", $this->blogConfig->getTitle());
        $this->assertEquals("Test blog footer", $this->blogConfig->getFooterText());
    }
}