<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\Collection\PagesConfigCollection;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use MarkdownBlog\IO\FileLoader;
use Symfony\Component\Yaml\Exception\ParseException;

class PagesConfigParser implements PagesConfigParserInterface
{
    private const REQUIRED_PROPERTIES = [
        'title',
        'markdown_file',
        'output_file',
    ];

    public function __construct(
        private YamlParserInterface $yamlParser,
        private FileLoader $fileLoader
    ) {

    }

    public function parse(string $yamlFilePath): PagesConfigCollection
    {
        $collection = new PagesConfigCollection();
        $yamlContents = $this->fileLoader->getFileContent($yamlFilePath);

        try {
            $config = $this->yamlParser->parse($yamlContents);
        } catch (ParseException $exception) {
            throw new UnableToParse(
                sprintf("Unable to parse config file: %s", $exception->getMessage())
            );
        }

        $this->validateConfig($config);

        foreach ($config['pages'] as $key => $page) {
            $collection->add(
                PageConfigDto::fromArray($page)
            );
        }

        return $collection;
    }

    private function validateConfig(array $config): void
    {
        if (false === array_key_exists('pages', $config)) {
            throw new InvalidConfiguration(
                "Pages configuration does not have `pages` block."
            );
        }

        foreach ($config['pages'] as $key => $page) {
            foreach (self::REQUIRED_PROPERTIES as $property) {
                if (false === array_key_exists($property, $page)) {
                    throw new InvalidConfiguration(
                        sprintf("Pages configuration block `%s` does not have `%s` property.", $key, $property)
                    );
                }
            }
        }
    }
}