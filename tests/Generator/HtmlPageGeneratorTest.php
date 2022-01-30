<?php

declare(strict_types=1);

namespace MarkdownBlog\Parser;

use MarkdownBlog\Generator\HtmlPageGenerator;
use MarkdownBlog\IO\FileLoaderInterface;
use MarkdownBlog\IO\FileWriterInterface;
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

        $this->fileLoader->expects($this->once())
            ->method("getFileContent")
            ->willReturn(
                $mdText
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

        $this->fileWriter->expects($this->once())
            ->method("saveFile")
            ->with(
                self::TEST_OUTPUT_DIR . self::TEST_OUTPUT_FILE,
                $generatedHtml
            );

        $this->generator->generate(
            markdownFile: "test_markdown_file",
            outputFile: self::TEST_OUTPUT_FILE
        );
    }
}