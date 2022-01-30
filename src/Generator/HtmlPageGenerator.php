<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\IO\FileLoaderInterface;
use MarkdownBlog\IO\FileWriterInterface;
use MarkdownBlog\Transformer\MarkdownToHtmlInterface;

class HtmlPageGenerator implements PageGeneratorInterface
{
    public function __construct(
        private string                  $markdownDir,
        private string                  $outputDir,
        private MarkdownToHtmlInterface $markdownToHtml,
        private FileLoaderInterface     $fileLoader,
        private FileWriterInterface     $fileWriter
    ) {

    }

    public function generate(string $markdownFile, string $outputFile): void
    {
        $markdown = $this->fileLoader->getFileContent($this->markdownDir . $markdownFile);

        $html = $this->markdownToHtml->transformText($markdown);

        $this->fileWriter->saveFile(
            $this->outputDir . $outputFile,
            $html
        );
    }
}