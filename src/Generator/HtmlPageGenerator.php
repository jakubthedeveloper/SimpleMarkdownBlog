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
        private string                  $templateDir,
        private MarkdownToHtmlInterface $markdownToHtml,
        private FileLoaderInterface     $fileLoader,
        private FileWriterInterface     $fileWriter
    ) {

    }

    public function generate(string $markdownFile, string $outputFile, string $templateFile): void
    {
        $markdown = $this->fileLoader->getFileContent($this->markdownDir . $markdownFile);
        $template = $this->fileLoader->getFileContent($this->templateDir . $templateFile);

        $pageHtml = $this->markdownToHtml->transformText($markdown);
        $fullHtml = $this->replaceTags($template, $pageHtml);

        $this->fileWriter->saveFile(
            $this->outputDir . $outputFile,
            $fullHtml
        );
    }

    private function replaceTags(string $template, string $pageHtml): string
    {
        $html = str_replace(
            '__PAGE_CONTENT__',
            $pageHtml,
            $template
        );

        // Replace another tags here

        // TODO: replace __PAGES_LIST__

        return $html;
    }
}