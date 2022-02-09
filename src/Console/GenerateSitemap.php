<?php

namespace MarkdownBlog\Console;

use MarkdownBlog\Config\BlogConfig;
use MarkdownBlog\Config\PagesConfig;
use MarkdownBlog\Generator\SitemapGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateSitemap extends Command
{
    protected static $defaultName = 'generate:sitemap';

    public function __construct(
        private readonly SitemapGeneratorInterface $sitemapGenerator
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sitemapGenerator->generate();

        return Command::SUCCESS;
    }
}
