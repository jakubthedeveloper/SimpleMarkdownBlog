<?php

declare(strict_types=1);

namespace MarkdownBlog\Generator;

use MarkdownBlog\Collection\PagesConfigCollection;
use MarkdownBlog\Config\PagesConfigInterface;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\IO\FileLoaderInterface;
use MarkdownBlog\IO\FileWriterInterface;
use MarkdownBlog\Parser\YamlParser;
use MarkdownBlog\Transformer\MarkdownToHtmlInterface;
use PHPUnit\Framework\TestCase;

/**
 * @property PagesConfigInterface|\PHPUnit\Framework\MockObject\MockObject $pagesConfig
 * @property PagesListGenerator $pagesListGenerator
 */
class PagesListGeneratorTest extends TestCase
{

    public function setUp(): void
    {
        $this->pagesConfig = $this->createMock(PagesConfigInterface::class);
        $this->pagesListGenerator = new PagesListGenerator(
            pagesConfig: $this->pagesConfig
        );
    }

    public function testPagesListHtml(): void
    {
        $collection = new PagesConfigCollection();

        $currentPage = new PageConfigDto(
            title: 'Current page',
            markdownFile: 'test.md',
            outputFile: 'current.html',
            templateFile: 'testx.html'
        );

        $collection->add(
            config: new PageConfigDto(
                title: 'First page',
                markdownFile: 'test.md',
                outputFile: 'first.html',
                templateFile: 'testx.html'
            )
        );

        $collection->add($currentPage);

        $collection->add(
            config: new PageConfigDto(
                title: 'Second page',
                markdownFile: 'test.md',
                outputFile: 'second.html',
                templateFile: 'testx.html'
            )
        );

        $this->pagesConfig->expects($this->once())
            ->method('getPagesConfig')
            ->willReturn($collection);

        $actual = $this->pagesListGenerator->generate(
            currentPage: $currentPage
        );

        $expected = <<<HTML
                    <ul class="pages-list">
                    <li><a href="first.html">First page</a></li>
                    <li><a href="second.html">Second page</a></li>
                    </ul>

                    HTML;

        $this->assertEquals($expected, $actual);
    }


    public function testShortPagesListHtml(): void
    {
        $currentPage = new PageConfigDto(
            title: 'Current page',
            markdownFile: 'current.md',
            outputFile: 'current.html',
            templateFile: 'test.html'
        );

        $collection = new PagesConfigCollection();

        $collection->add(
            config: new PageConfigDto(
                title: 'First page',
                markdownFile: 'test.md',
                outputFile: 'first.html',
                templateFile: 'test.html'
            )
        );

        $collection->add($currentPage);

        $collection->add(
            config: new PageConfigDto(
                title: 'Second page',
                markdownFile: 'test.md',
                outputFile: 'second.html',
                templateFile: 'test.html'
            )
        );

        $collection->add(
            config: new PageConfigDto(
                title: 'Third page',
                markdownFile: 'test.md',
                outputFile: 'third.html',
                templateFile: 'test.html'
            )
        );

        $this->pagesConfig->expects($this->once())
            ->method('getPagesConfig')
            ->willReturn($collection);

        $actual = $this->pagesListGenerator->generateShort(
            currentPage: $currentPage,
            limit: 2
        );

        $expected = <<<HTML
                    <ul class="pages-list">
                    <li><a href="first.html">First page</a></li>
                    <li><a href="second.html">Second page</a></li>
                    </ul>

                    HTML;

        $this->assertEquals($expected, $actual);
    }
}