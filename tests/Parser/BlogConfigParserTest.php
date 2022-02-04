<?php

declare(strict_types=1);

namespace MarkdownBlog\Parser;

use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Exception\UnableToParse;
use MarkdownBlog\IO\FileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * @property YamlParserInterface|\PHPUnit\Framework\MockObject\MockObject $yamlParser
 * @property FileLoader|\PHPUnit\Framework\MockObject\MockObject $fileLoader
 * @property BlogConfigParser $blogConfigParser
 */
class BlogConfigParserTest extends TestCase
{
    public function setUp(): void
    {
        $this->yamlParser = $this->createMock(YamlParserInterface::class);
        $this->fileLoader = $this->createMock(FileLoader::class);

        $this->blogConfigParser = new BlogConfigParser(
            yamlParser: $this->yamlParser,
            fileLoader: $this->fileLoader
        );
    }

    public function testValidConfigurationIsParsedSuccessfully(): void
    {
        $blogData = [
            'blog' => [
                'title' => 'My awesome blog.'
            ]
        ];

        $this->fileLoader->expects($this->once())
            ->method("getFileContent")
            ->willReturn("Parsed content");

        $this->yamlParser->expects($this->once())
            ->method("parse")
            ->willReturn($blogData);

        $actual = $this->blogConfigParser->parse("blog.yaml");

        $this->assertEquals("My awesome blog.", $actual->title);
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

        iterator_to_array($this->blogConfigParser->parse("some.yaml"));
    }

    public function testExceptionIfBlogSectionDoesNotExist(): void
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
        $this->expectDeprecationMessage("Blog configuration must have a root element 'blog'.");

        iterator_to_array($this->blogConfigParser->parse("some.yaml"));
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
            sprintf("log configuration property `%s` is not set.", $missingProperty)
        );

        iterator_to_array($this->blogConfigParser->parse("some.yaml"));
    }

    private function requiredPropertyExceptions(): array
    {
        return [
            [
                [
                    'blog' => ['some_field' => 'some_value']
                ],
                'title'
            ]
        ];
    }
}