<?php

namespace MarkdownBlog\Console;

use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Parser\ConfigParserInterface;
use MarkdownBlog\Transformer\MarkdownToHtmlInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePages extends Command
{
    protected static $defaultName = 'generate:pages';

    public function __construct(
        private string                  $configDir,
        private string                  $markdownDir,
        private string                  $outputDir,
        private ConfigParserInterface   $configParser,
        private MarkdownToHtmlInterface $markdownToHtml
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
            if (false === file_exists($this->markdownDir . $page['markdown_file'])) {
                throw new \Exception(
                    sprintf("Markdown file %s does not exist.", $page['markdown_file'])
                );
            }

            $markdown = file_get_contents($this->markdownDir . $page['markdown_file']);
            $html = $this->markdownToHtml->transformText($markdown);
            file_put_contents(
                $this->outputDir . $page['output_file'],
                $html
            );
        }
    }
}
