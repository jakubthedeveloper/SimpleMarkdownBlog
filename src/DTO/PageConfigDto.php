<?php

namespace MarkdownBlog\DTO;

use JetBrains\PhpStorm\Pure;

class PageConfigDto
{
    private const DEFAULT_TEMPLATE = 'single.html';

    public function __construct(
        public readonly string $title,
        public readonly string $markdownFile,
        public readonly string $outputFile,
        public readonly string $templateFile = self::DEFAULT_TEMPLATE,
        public readonly ?string $description,
        public readonly ?string $image
    ) {

    }

    #[Pure] public static function fromArray(array $pageData): self
    {
        return new self(
            $pageData['title'],
            $pageData['markdown_file'],
            $pageData['output_file'],
            $pageData['template_file'] ?? self::DEFAULT_TEMPLATE,
            $pageData['description'] ?? null,
            $pageData['image'] ?? null
        );
    }
}