<?php

namespace Venyii\WhosePoints\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Venyii\WhosePoints\LocalStaticGenerator;

class GenerateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('wliia:generate-pages')
            ->addOption(
                'outdir',
                'o',
                InputOption::VALUE_REQUIRED,
                'Where to put the generated content?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outDir = $input->getOption('outdir');

        $generator = new LocalStaticGenerator($outDir);
        $generator->generate()->write();

        $output->writeln('Written to: '.$outDir);
    }
}
