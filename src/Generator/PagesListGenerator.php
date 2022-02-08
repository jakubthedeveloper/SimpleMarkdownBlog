<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\Config\PagesConfigInterface;
use MarkdownBlog\DTO\PageConfigDto;

class PagesListGenerator implements ListGeneratorInterface
{
    public function __construct(
        private PagesConfigInterface $pagesConfig
    ) {

    }

    public function generateShort(PageConfigDto $currentPage, int $limit): string
    {
        return $this->generate($currentPage, $limit);
    }

    public function generate(PageConfigDto $currentPage, ?int $limit = null): string
    {
        $result = '<ul class="pages-list">';
        $result .= "\n";

        $i = 0;
        /** @var PageConfigDto $page */
        foreach ($this->pagesConfig->getPagesConfig()->all() as $page) {
            if ($limit !== null && $i >= $limit) {
                break;
            }

            if ($page->outputFile === $currentPage->outputFile) {
                continue;
            }

            $result .= sprintf(
                '<li><a href="%s">%s</a></li>',
                $page->outputFile,
                $page->title
            );
            $result .= "\n";

            $i++;
        }

        $result .= '</ul>';
        $result .= "\n";

        return $result;
    }
}