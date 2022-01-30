<?php

declare(strict_types=1);

namespace MarkdownBlog\Parser;

use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use MarkdownBlog\IO\FileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @property YamlParserInterface|\PHPUnit\Framework\MockObject\MockObject $yamlParser
 * @property FileLoader|\PHPUnit\Framework\MockObject\MockObject $fileLoader
 * @property PagesConfigParser $pagesConfigParser
 */
class PagesConfigParserTest extends TestCase
{
    public function setUp(): void
    {
        $this->yamlParser = $this->createMock(YamlParserInterface::class);
        $this->fileLoader = $this->createMock(FileLoader::class);

        $this->pagesConfigParser = new PagesConfigParser(
            yamlParser: $this->yamlParser,
            fileLoader: $this->fileLoader
        );
    }

    public function testValidConfigurationIsParsedSuccessfully(): void
    {
        $pagesData = [
            'pages' => [
                'index' => [
                    'title' => 'My first page',
                    'markdown_file' => 'index.md',
                    'output_file' => 'index.html'
                ],
                'about' => [
                    'title' => 'About',
                    'markdown_file' => 'about.md',
                    'output_file' => 'about.html',
                    'template_file' => 'some.html'
                ]
            ]
        ];

        $this->fileLoader->expects($this->once())
            ->method("getFileContent")
            ->willReturn("Parsed content");

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($pagesData);

        $actual = $this->pagesConfigParser->parse("pages.yaml");
        $actual = iterator_to_array($actual);

        $this->assertInstanceOf(PageConfigDto::class, $actual[0]);
        $this->assertEquals($pagesData['pages']['index']['title'], $actual[0]->title);
        $this->assertEquals($pagesData['pages']['index']['markdown_file'], $actual[0]->markdownFile);
        $this->assertEquals($pagesData['pages']['index']['output_file'], $actual[0]->outputFile);
        $this->assertEquals('single.html', $actual[0]->templateFile); // default

        $this->assertInstanceOf(PageConfigDto::class, $actual[1]);
        $this->assertEquals($pagesData['pages']['about']['title'], $actual[1]->title);
        $this->assertEquals($pagesData['pages']['about']['markdown_file'], $actual[1]->markdownFile);
        $this->assertEquals($pagesData['pages']['about']['output_file'], $actual[1]->outputFile);
        $this->assertEquals('some.html', $actual[1]->templateFile);
    }

    public function testCannotParseInvalidYaml(): void
    {
        $this->fileLoader->expects($this->once())
            ->method("getFileContent")
            ->willReturn("Parsed content");

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willThrowException(
                new ParseException("I can't parse this yaml.")
            );

        $this->expectException(UnableToParse::class);
        $this->expectDeprecationMessage("Unable to parse config file: I can't parse this yaml.");

        iterator_to_array($this->pagesConfigParser->parse("some.yaml"));
    }

    public function testExceptionIfPagesSectionDoesNotExist(): void
    {
        $this->fileLoader->expects($this->once())
            ->method("getFileContent")
            ->willReturn("Parsed content");

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn([
                'something' => [
                    'some_key' => 'some_value'
                ]
            ]);

        $this->expectException(InvalidConfiguration::class);
        $this->expectDeprecationMessage("Pages configuration does not have `pages` block.");

        iterator_to_array($this->pagesConfigParser->parse("some.yaml"));
    }

    /**
     * @dataProvider requiredPropertyExceptions
     */
    public function testExceptionIfRequiredPropertyIsMissing(array $parsedData, string $missingProperty): void
    {
        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($parsedData);

        $this->expectException(InvalidConfiguration::class);
        $this->expectDeprecationMessage(
            sprintf("Pages configuration block `index` does not have `%s` property.", $missingProperty)
        );

        iterator_to_array($this->pagesConfigParser->parse("some.yaml"));
    }

    private function requiredPropertyExceptions(): array
    {
        return [
            [
                [
                    'pages' => [
                        'index' => [
                            'output_file' => 'index.html',
                            'markdown_file' => 'index.md'
                        ]
                    ]
                ],
                'title'
            ],
            [
                [
                    'pages' => [
                        'index' => [
                            'title' => 'My page',
                            'markdown_file' => 'index.md'
                        ]
                    ]
                ],
                'output_file'
            ],
            [
                [
                    'pages' => [
                        'index' => [
                            'title' => 'My page',
                            'output_file' => 'index.html',
                        ]
                    ]
                ],
                'markdown_file'
            ],
        ];
    }
}