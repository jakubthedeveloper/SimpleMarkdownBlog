<?php

declare(strict_types=1);

namespace MarkdownBlog\Generator;

use MarkdownBlog\Config\BlogConfigInterface;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\IO\FileLoaderInterface;
use MarkdownBlog\IO\FileWriterInterface;
use MarkdownBlog\Parser\YamlParser;
use MarkdownBlog\Transformer\MarkdownToHtmlInterface;
use PHPUnit\Framework\TestCase;

/**
 * @property YamlParser $yamlParser
 * @property MarkdownToHtmlInterface|\PHPUnit\Framework\MockObject\MockObject $markdownToHtml
 * @property FileLoaderInterface|\PHPUnit\Framework\MockObject\MockObject $fileLoader
 * @property FileWriterInterface|\PHPUnit\Framework\MockObject\MockObject $fileWriter
 * @property ListGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject $pagesListGenerator
 * @property HtmlPageGenerator $generator
 * @property BlogConfigInterface|\PHPUnit\Framework\MockObject\MockObject $blogConfig
 */
class HtmlPageGeneratorTest extends TestCase
{
    const TEST_OUTPUT_FILE = "test_output_file.html";
    const TEST_OUTPUT_DIR = "test_output_dir/";

    public function setUp(): void
    {
        $this->markdownToHtml = $this->createMock(MarkdownToHtmlInterface::class);
        $this->fileLoader = $this->createMock(FileLoaderInterface::class);
        $this->fileWriter = $this->createMock(FileWriterInterface::class);
        $this->pagesListGenerator = $this->createMock(ListGeneratorInterface::class);
        $this->blogConfig = $this->createMock(BlogConfigInterface::class);

        $this->generator = new HtmlPageGenerator(
            markdownDir: "test_markdown_dir",
            outputDir: self::TEST_OUTPUT_DIR,
            templateDir: "test_template_dir",
            markdownToHtml: $this->markdownToHtml,
            fileLoader: $this->fileLoader,
            fileWriter: $this->fileWriter,
            pagesListGenerator: $this->pagesListGenerator,
            blogConfig: $this->blogConfig
        );
    }

    /**
     * @dataProvider generatePageData
     */
    public function testGeneratePage(string $htmlTemplate, string $generatedHtml, string $expectedHtml): void
    {
        $mdText = "some markdown";

        $this->fileLoader->expects($this->exactly(2))
            ->method("getFileContent")
            ->willReturnOnConsecutiveCalls(
                $mdText,
                $htmlTemplate
            );

        $this->markdownToHtml->expects($this->once())
            ->method("transformText")
            ->with(
                $mdText
            )
            ->willReturn(
                $generatedHtml
            );

        $this->pagesListGenerator->expects($this->any())
            ->method("generate")
            ->willReturn('<a href="first.html">first</a><a href="second.html">second</a>');

        $this->pagesListGenerator->expects($this->any())
            ->method("generateShort")
            ->willReturn('<a href="first.html">first</a>');

        $this->blogConfig->expects($this->any())
            ->method('getTitle')
            ->willReturn('My blog title');

        $this->blogConfig->expects($this->any())
            ->method('getBaseUrl')
            ->willReturn('https://blog.test');

        $this->blogConfig->expects($this->any())
            ->method('getFooterText')
            ->willReturn('&copy; My footer');

        $this->fileWriter->expects($this->once())
            ->method("saveFile")
            ->with(
                self::TEST_OUTPUT_DIR . self::TEST_OUTPUT_FILE,
                $expectedHtml
            );

        $this->generator->generate(
            new PageConfigDto(
                title: "test title",
                markdownFile: "test_markdown_file",
                outputFile: self::TEST_OUTPUT_FILE,
                templateFile: "test_template_file",
                description: "test description",
                image: "test_image.png",
                type: "article"
            )
        );
    }

    private function generatePageData(): array
    {
        return [
            [
                <<<HTML
                <html>
                    <head></head><body>__PAGE_CONTENT__</body>
                </html>
                HTML,
                <<<HTML
                <h1>Test header</h1><p>Test content</p>
                HTML,
                <<<HTML
                <html>
                    <head></head><body><h1>Test header</h1><p>Test content</p></body>
                </html>
                HTML
            ],
            [
                <<<HTML
                <html>
                    <head></head>
                    <body>
                    __PAGES_LIST__
                    <div>__PAGE_CONTENT__</div>
                    <div>__PAGES_LIST_SHORT__</div>
                </body>
                </html>
                HTML,
                <<<HTML
                <h1>Test header</h1><p>Test content</p>
                HTML,
                <<<HTML
                <html>
                    <head></head>
                    <body>
                    <a href="first.html">first</a><a href="second.html">second</a>
                    <div><h1>Test header</h1><p>Test content</p></div>
                    <div><a href="first.html">first</a><a href="second.html">second</a></div>
                </body>
                </html>
                HTML
            ],
            [
                <<<HTML
                <html>
                    <head><title>__TITLE__</title></head><body><h1>__TITLE__</h1> <h2>__DESCRIPTION__</h2> <img src="__IMAGE__" /> <p>__PAGE_CONTENT__</p></body>
                </html>
                HTML,
                <<<HTML
                Test content
                HTML,
                <<<HTML
                <html>
                    <head><title>test title</title></head><body><h1>test title</h1> <h2>test description</h2> <img src="https://blog.test/images/test_image.png" /> <p>Test content</p></body>
                </html>
                HTML
            ],
            [
                <<<HTML
                <html>
                    <head>
                        <title>__TITLE__</title>
                        <meta property="og:url" content="__PAGE_URL__" />
                        <meta property="og:type" content="__PAGE_TYPE__" />
                    </head>
                    <body><h1>__BLOG_TITLE__</h1> __PAGE_CONTENT__ <div class="footer">__FOOTER_TEXT__</div></body>
                </html>
                HTML,
                <<<HTML
                Test content
                HTML,
                <<<HTML
                <html>
                    <head>
                        <title>test title</title>
                        <meta property="og:url" content="https://blog.test/test_output_file.html" />
                        <meta property="og:type" content="article" />
                    </head>
                    <body><h1>My blog title</h1> Test content <div class="footer">&copy; My footer</div></body>
                </html>
                HTML
            ]
        ];
    }
}