<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\DTO\BlogConfigDto;
use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use MarkdownBlog\IO\FileLoader;
use Symfony\Component\Yaml\Exception\ParseException;

class BlogConfigParser implements BlogConfigParserInterface
{
    private const REQUIRED_PROPERTIES = [
        'title',
        'footer_text',
        'short_pages_list_items_count'
    ];

    public function __construct(
        private YamlParserInterface $yamlParser,
        private FileLoader $fileLoader
    ) {

    }

    public function parse(string $yamlFilePath): BlogConfigDto
    {
        $yamlContents = $this->fileLoader->getFileContent($yamlFilePath);

        try {
            $config = $this->yamlParser->parse($yamlContents);
        } catch (ParseException $exception) {
            throw new UnableToParse(
                sprintf("Unable to parse config file: %s", $exception->getMessage())
            );
        }

        $this->validateConfig($config);

        return new BlogConfigDto(
            title: $config['blog']['title'],
            footerText: $config['blog']['footer_text'],
            shortPagesListItemsCount: $config['blog']['short_pages_list_items_count']
        );
    }

    private function validateConfig(array $config): void
    {
        if (false === array_key_exists('blog', $config)) {
            throw new InvalidConfiguration("Blog configuration must have a root element 'blog'.");
        }

        foreach (self::REQUIRED_PROPERTIES as $property) {
            if (false === array_key_exists($property, $config['blog'])) {
                throw new InvalidConfiguration(
                    sprintf("Blog configuration property `%s` is not set.", $property)
                );
            }
        }
    }
}