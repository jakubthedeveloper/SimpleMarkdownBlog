<?php

declare(strict_types=1);

namespace MarkdownBlog\Generator;

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
 * @property HtmlPageGenerator $generator
 */
class HtmlPageGeneratorTest extends TestCase
{
    const TEST_OUTPUT_FILE = "test_output_file";
    const TEST_OUTPUT_DIR = "test_output_dir/";

    public function setUp(): void
    {
        $this->markdownToHtml = $this->createMock(MarkdownToHtmlInterface::class);
        $this->fileLoader = $this->createMock(FileLoaderInterface::class);
        $this->fileWriter = $this->createMock(FileWriterInterface::class);

        $this->generator = new HtmlPageGenerator(
            markdownDir: "test_markdown_dir",
            outputDir: self::TEST_OUTPUT_DIR,
            templateDir: "test_template_dir",
            markdownToHtml: $this->markdownToHtml,
            fileLoader: $this->fileLoader,
            fileWriter: $this->fileWriter
        );
    }

    public function testGeneratePage(): void
    {
        $mdText = <<<MD
                # Test header
                
                Test content
                MD;

        $htmlTemplate = <<<MD
        <html>
            <head></head>
            <body>__PAGE_CONTENT__</body>
        </html>
        MD;

        $this->fileLoader->expects($this->exactly(2))
            ->method("getFileContent")
            ->willReturnOnConsecutiveCalls(
                $mdText,
                $htmlTemplate
            );

        $generatedHtml = <<<HTML
                <h1>Test header</h1>
                <p>Test content</p>
                HTML;

        $this->markdownToHtml->expects($this->once())
            ->method("transformText")
            ->with(
                $mdText
            )
            ->willReturn(
                $generatedHtml
            );

        $expectedHtml = <<<HTML
        <html>
            <head></head>
            <body><h1>Test header</h1>
        <p>Test content</p></body>
        </html>
        HTML;


        $this->fileWriter->expects($this->once())
            ->method("saveFile")
            ->with(
                self::TEST_OUTPUT_DIR . self::TEST_OUTPUT_FILE,
                $expectedHtml
            );

        $this->generator->generate(
            markdownFile: "test_markdown_file",
            outputFile: self::TEST_OUTPUT_FILE,
            templateFile: "test_template_file"
        );
    }
}