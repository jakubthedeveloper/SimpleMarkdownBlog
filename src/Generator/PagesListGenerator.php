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

    public function generate(): string
    {
        $result = '<ul class="pages-list">';
        $result .= "\n";

        /**
         * @var PageConfigDto $page
         */
        foreach ($this->pagesConfig->getPagesConfig()->all() as $page) {
            $result .= sprintf(
                '<li><a href="%s">%s</a></li>',
                $page->outputFile,
                $page->title
            );
            $result .= "\n";
        }

        $result .= '</ul>';
        $result .= "\n";

        return $result;
    }
}