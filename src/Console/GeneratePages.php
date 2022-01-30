<?php

namespace MarkdownBlog\Console;

use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Generator\PageGeneratorInterface;
use MarkdownBlog\Parser\ConfigParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePages extends Command
{
    protected static $defaultName = 'generate:pages';
    private const DEFAULT_TEMPLATE = 'single.html';

    public function __construct(
        private string                  $configDir,
        private string                  $templatesDir,
        private string                  $publicDir,
        private ConfigParserInterface   $configParser,
        private PageGeneratorInterface  $pageGenerator
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getPagesConfig();
        $this->generateHtml($config);

        copy(
            $this->templatesDir . '/css/main.css',
            $this->publicDir . '/css/main.css'
        );

        return Command::SUCCESS;
    }

    private function getPagesConfig(): array
    {
        if (false === file_exists($this->configDir . '/pages.yaml')) {
            throw new InvalidConfiguration("Pages config file does not exist.");
        }

        $yamlContents = file_get_contents($this->configDir . '/pages.yaml');

        return $this->configParser->parse($yamlContents);
    }

    private function generateHtml(array $config): void
    {
        foreach ($config['pages'] as $key => $page)
        {
            $this->pageGenerator->generate(
                markdownFile: $page['markdown_file'],
                outputFile: $page['output_file'],
                templateFile: $page['template_file'] ?? self::DEFAULT_TEMPLATE
            );
        }
    }
}
