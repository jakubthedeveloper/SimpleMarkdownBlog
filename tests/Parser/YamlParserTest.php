<?php

declare(strict_types=1);

namespace MarkdownBlog\Parser;

use PHPUnit\Framework\TestCase;

/**
 * @property YamlParser $yamlParser
 */
class YamlParserTest extends TestCase
{

    public function setUp(): void
    {
        $this->yamlParser = new YamlParser();
    }

    public function testParserReturnsParsedData(): void
    {
        $yaml = <<<YAML
        pages:
          index:
            markdown_file: index.md
            output_file: index.html
          about:
            markdown_file: about.md
            output_file: about.html
        YAML;

        $expected = [
            'pages' => [
                'index' => [
                    'markdown_file' => 'index.md',
                    'output_file' => 'index.html'
                ],
                'about' => [
                    'markdown_file' => 'about.md',
                    'output_file' => 'about.html'
                ]
            ]
        ];

        $actual = $this->yamlParser->parse(
            yamlContents: $yaml
        );

        $this->assertEquals($expected, $actual);
    }
}