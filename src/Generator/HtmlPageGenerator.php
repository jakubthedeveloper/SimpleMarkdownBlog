<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\Config\BlogConfigInterface;
use MarkdownBlog\DTO\PageConfigDto;
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
        private FileWriterInterface     $fileWriter,
        private ListGeneratorInterface  $pagesListGenerator,
        private BlogConfigInterface     $blogConfig
    ) {

    }

    public function generate(PageConfigDto $page): void
    {
        $markdown = $this->fileLoader->getFileContent($this->markdownDir . $page->markdownFile);
        $template = $this->fileLoader->getFileContent($this->templateDir . $page->templateFile);

        $pageHtml = $this->markdownToHtml->transformText($markdown);
        $fullHtml = $this->replaceTags($template, $pageHtml, $page);

        $this->fileWriter->saveFile(
            $this->outputDir . $page->outputFile,
            $fullHtml
        );
    }

    private function replaceTags(string $template, string $pageHtml, PageConfigDto $page): string
    {
        $html = str_replace(
            '__PAGE_CONTENT__',
            $pageHtml,
            $template
        );

        if (false !== stripos($html, '__PAGES_LIST__')) {
            $pagesList = $this->pagesListGenerator->generate(
                currentPage: $page
            );

            $html = str_replace(
                '__PAGES_LIST__',
                $pagesList,
                $html
            );
        }

        if (false !== stripos($html, '__PAGES_LIST_SHORT__')) {
            $pagesList = $this->pagesListGenerator->generate(
                currentPage: $page,
                limit: $this->blogConfig->getShortPagesListItemsCount()
            );

            $html = str_replace(
                '__PAGES_LIST_SHORT__',
                $pagesList,
                $html
            );
        }

        if (false !== stripos($html, '__TITLE__')) {
            $html = str_replace(
                '__TITLE__',
                $page->title,
                $html
            );
        }

        if (false !== stripos($html, '__DESCRIPTION__')) {
            $html = str_replace(
                '__DESCRIPTION__',
                $page->description ?? '',
                $html
            );
        }

        if (false !== stripos($html, '__IMAGE__')) {
            $html = str_replace(
                '__IMAGE__',
                $page->image ? './images/' . $page->image : '',
                $html
            );
        }

        if (false !== stripos($html, '__BLOG_TITLE__')) {
            $html = str_replace(
                '__BLOG_TITLE__',
                $this->blogConfig->getTitle(),
                $html
            );
        }

        if (false !== stripos($html, '__FOOTER_TEXT__')) {
            $html = str_replace(
                '__FOOTER_TEXT__',
                $this->blogConfig->getFooterText(),
                $html
            );
        }

        // Replace another tags here

        return $html;
    }
}