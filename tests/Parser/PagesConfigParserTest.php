<?php

declare(strict_types=1);

namespace MarkdownBlog\Parser;

use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @property YamlParserInterface|\PHPUnit\Framework\MockObject\MockObject $yamlParser
 * @property PagesConfigParser $pagesConfigParser
 */
class PagesConfigParserTest extends TestCase
{
    public function setUp(): void
    {
        $this->yamlParser = $this->createMock(YamlParserInterface::class);
        $this->pagesConfigParser = new PagesConfigParser(
            $this->yamlParser
        );
    }

    public function testValidConfigurationIsParsedSuccessfully(): void
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

        $parsed = [
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

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($parsed);

        $actual = $this->pagesConfigParser->parse(
            yamlContents: $yaml
        );

        $this->assertEquals($parsed, $actual);
    }

    public function testCannotParseInvalidYaml(): void
    {
        $yaml = <<<YAML
        some invalid yaml
        YAML;

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willThrowException(
               new ParseException("I can't parse this yaml.")
            );

        $this->expectException(UnableToParse::class);
        $this->expectDeprecationMessage("Unable to parse config file: I can't parse this yaml.");

        $this->pagesConfigParser->parse(
            yamlContents: $yaml
        );
    }

    public function testExceptionIfPagesSectionDoesNotExist(): void
    {
        $yaml = <<<YAML
        something:
            some_key: some_value
        YAML;

        $this->expectException(InvalidConfiguration::class);
        $this->expectDeprecationMessage("Pages configuration does not have `pages` block.");

        $this->pagesConfigParser->parse(
            yamlContents: $yaml
        );
    }

    public function testExceptionIfMarkdownFilePropertyIsMissing(): void
    {
        $yaml = <<<YAML
        pages:
          index:
            output_file: index.html
        YAML;

        $parsed = [
            'pages' => [
                'index' => [
                    'output_file' => 'index.html'
                ]
            ]
        ];

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($parsed);

        $this->expectException(InvalidConfiguration::class);
        $this->expectDeprecationMessage("Pages configuration block `index` does not have `markdown_file` property.");

        $this->pagesConfigParser->parse(
            yamlContents: $yaml
        );
    }

    public function testExceptionIfOutputFilePropertyIsMissing(): void
    {
        $yaml = <<<YAML
        pages:
          index:
            markdown_file: index.md
        YAML;

        $parsed = [
            'pages' => [
                'index' => [
                    'markdown_file' => 'index.md'
                ]
            ]
        ];

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($parsed);

        $this->expectException(InvalidConfiguration::class);
        $this->expectDeprecationMessage("Pages configuration block `index` does not have `output_file` property.");

        $this->pagesConfigParser->parse(
            yamlContents: $yaml
        );
    }
}