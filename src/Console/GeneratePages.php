<?php

namespace MarkdownBlog\Console;

use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Exception\InvalidConfiguration;
use MarkdownBlog\Generator\PageGeneratorInterface;
use MarkdownBlog\Parser\PagesConfigParserInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePages extends Command
{
    protected static $defaultName = 'generate:pages';

    public function __construct(
        private string                     $configDir,
        private string                     $templatesDir,
        private string                     $publicDir,
        private PagesConfigParserInterface $pagesConfigParser,
        private PageGeneratorInterface     $pageGenerator
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

    /**
     * @return iterable|PageConfigDto[]
     */
    private function getPagesConfig(): iterable
    {
        return $this->pagesConfigParser->parse($this->configDir . '/pages.yaml');
    }

    private function generateHtml(iterable $pages): void
    {
        /**
         * @var PageConfigDto $page
         */
        foreach ($pages as $page)
        {
            $this->pageGenerator->generate($page);
        }
    }
}
