<?php

namespace Venyii\WhosePoints\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Venyii\WhosePoints\LocalStaticGenerator;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wpata:generate')
            ->addArgument('outdir', InputArgument::REQUIRED, 'Where to put the generated content?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $outDir = $input->getArgument('outdir');

        if (!is_dir($outDir)) {
            (new Filesystem())->mkdir($outDir, 0755);
        }

        $generator = new LocalStaticGenerator($outDir);
        $generator->generate()->write();

        $io->success('Written to: '.realpath($outDir));

        return Command::SUCCESS;
    }
}
