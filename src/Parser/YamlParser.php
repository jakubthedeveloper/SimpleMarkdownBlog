<?php

namespace MarkdownBlog\Parser;

use Symfony\Component\Yaml\Yaml;

class YamlParser implements YamlParserInterface
{
    public function parse(string $yamlContents): array
    {
        $parsed = Yaml::parse($yamlContents);

        return (array)$parsed;
    }
}