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

    public function __construct(
        private string                  $configDir,
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
                $page['markdown_file'],
                $page['output_file']
            );
        }
    }
}
