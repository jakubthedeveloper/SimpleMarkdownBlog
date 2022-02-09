<?php

declare(strict_types=1);

namespace MarkdownBlog\Config;

use MarkdownBlog\Collection\PagesConfigCollection;
use MarkdownBlog\DTO\BlogConfigDto;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Parser\BlogConfigParserInterface;
use MarkdownBlog\Parser\PagesConfigParserInterface;
use PHPUnit\Framework\TestCase;

/**
 * @property PagesConfigParserInterface|\PHPUnit\Framework\MockObject\MockObject $pagesConfigParser
 * @property PagesConfig $blogConfig
 */
class PagesConfigTest extends TestCase
{
    public function setUp(): void
    {
        $this->pagesConfigParser = $this->createMock(PagesConfigParserInterface::class);

        $this->pagesConfig = new PagesConfig(
            configDir: 'config/',
            pagesConfigParser: $this->pagesConfigParser
        );
    }

    public function testPagesConfiguration(): void
    {
        $collection = new PagesConfigCollection();

        $collection->add(
            config: new PageConfigDto(
                title: 'First page',
                markdownFile: 'test.md',
                outputFile: 'first.html',
                templateFile: 'testx.html'
            )
        );

        $this->pagesConfigParser->expects($this->once())
            ->method("parse")
            ->willReturn($collection);

        $result = $this->pagesConfig->getPagesConfig();

        $this->assertEquals($collection, $result);
    }
}