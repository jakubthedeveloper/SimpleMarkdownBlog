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

    public function generateShort(int $limit): string
    {
        return $this->generate($limit);
    }

    public function generate(?int $limit = null): string
    {
        $result = '<ul class="pages-list">';
        $result .= "\n";

        $i = 0;
        /** @var PageConfigDto $page */
        foreach ($this->pagesConfig->getPagesConfig()->all() as $page) {
            if ($limit !== null && $i >= $limit) {
                break;
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