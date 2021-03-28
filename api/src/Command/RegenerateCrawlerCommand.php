<?php

namespace App\Command;

use Exception;
use App\Entity\Crawler;
use Symfony\Component\Finder\Finder;
use App\Repository\CrawlerRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegenerateCrawlerCommand extends Command
{
    protected static $defaultName = 'RegenerateCrawler';
    protected static $defaultDescription = 'Add a short description for your command';

    private string $rootPath = '';

    private ?Finder $finder = null;

    public function __construct(
        private String $projectDir,
        private Filesystem $filesystem,
        private CrawlerRepository $crawlerRepository,
    ) {
        $this->finder = new Finder();

        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;

        $this->rootPath = $this->projectDir.'/src/';
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->comment('start crawler regenerations');
        
        $crawlerDir = $this->checkOrCreateCrawlerDirectory($io);

        $sameNumberOfCrawlerCondition = $this->getNbCrawlerInDB() === $this->getNbCrawlerInDirectory($crawlerDir);
        if ($sameNumberOfCrawlerCondition) {
            $io->block('No Crawler, to generate');
            $io->success('Done');
            
            return Command::SUCCESS;
        }

        // TODO Drop all crawler entity and regenerate
        return Command::SUCCESS;
    }

    private function checkOrCreateCrawlerDirectory(SymfonyStyle $io): string
    {
        if (!$this->filesystem->exists($this->rootPath.'Crawlers')) {
            $this->filesystem->mkdir($this->rootPath.'Crawlers');
            $io->info('Crawlers directory created');
        }

        return $this->rootPath.'Crawlers/';
    }

    private function getNbCrawlerInDirectory(string $path): int
    {
        return $this->finder->files()->in($path)->count();
    }

    private function getNbCrawlerInDB(): int
    {
        return $this->crawlerRepository->count([]);
    }
}