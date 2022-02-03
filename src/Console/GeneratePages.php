<?php

namespace MarkdownBlog\Console;

use MarkdownBlog\Collection\PagesConfigCollection;
use MarkdownBlog\Config\PagesConfigInterface;
use MarkdownBlog\DTO\PageConfigDto;
use MarkdownBlog\Generator\PageGeneratorInterface;
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
        private PagesConfigInterface       $pagesConfig,
        private PageGeneratorInterface     $pageGenerator
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->pagesConfig->getPagesConfig();
        $this->generateHtml($config);

        copy(
            $this->templatesDir . '/css/main.css',
            $this->publicDir . '/css/main.css'
        );

        return Command::SUCCESS;
    }

    private function generateHtml(PagesConfigCollection $pages): void
    {
        /**
         * @var PageConfigDto $page
         */
        foreach ($pages->all() as $page)
        {
            $this->pageGenerator->generate($page);
        }
    }
}
