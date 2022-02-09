<?php

namespace MarkdownBlog\Generator;

use MarkdownBlog\Config\BlogConfig;
use MarkdownBlog\Config\PagesConfig;

class SitemapGenerator implements SitemapGeneratorInterface
{
    public function __construct(
        private readonly BlogConfig $blogConfig,
        private readonly PagesConfig $pagesConfig,
        private readonly string $outputDir
    ) {

    }

    public function generate(): void
    {
        $generator = new \Icamys\SitemapGenerator\SitemapGenerator($this->blogConfig->getBaseUrl(), $this->outputDir);

        $generator->enableCompression();
        $generator->setMaxUrlsPerSitemap(50000);
        $generator->setSitemapFileName("sitemap.xml");
        $generator->setSitemapIndexFileName("sitemap-index.xml");

        foreach ($this->pagesConfig->getPagesConfig()->all() as $page) {
            $generator->addURL(
                path: '/' . $page->outputFile,
                lastModified: new \DateTime(),
                changeFrequency: 'daily',
                priority: 0.5
            );
        }

        $generator->flush();
        $generator->finalize();

        $generator->updateRobots();
        $generator->submitSitemap();
    }
}