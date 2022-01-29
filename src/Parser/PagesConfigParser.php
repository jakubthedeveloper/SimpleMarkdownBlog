<?php

namespace MarkdownBlog\Parser;

use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use Symfony\Component\Yaml\Exception\ParseException;

class PagesConfigParser implements ConfigParserInterface
{
    public function __construct(
        private YamlParserInterface $yamlParser
    ) {

    }

    public function parse(string $yamlContents): array
    {
        try {
            $config = $this->yamlParser->parse($yamlContents);
        } catch (ParseException $exception) {
            throw new UnableToParse(
                sprintf("Unable to parse config file: %s", $exception->getMessage())
            );
        }

        $this->validateConfig($config);

        return $config;
    }

    private function validateConfig(array $config): void
    {
        if (false === array_key_exists('pages', $config)) {
            throw new InvalidConfiguration(
                "Pages configuration does not have `pages` block."
            );
        }

        foreach ($config['pages'] as $key => $page) {
            if (false === array_key_exists('markdown_file', $page)) {
                throw new InvalidConfiguration(
                    sprintf("Pages configuration block `%s` does not have `markdown_file` property.", $key)
                );
            }

            if (false === array_key_exists('output_file', $page)) {
                throw new InvalidConfiguration(
                    sprintf("Pages configuration block `%s` does not have `output_file` property.", $key)
                );
            }
        }
    }
}