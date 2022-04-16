<?php

namespace App\Command;

use App\Service\SplitterService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:split',
    description: 'Split something format to somewhat format',
)]
class SplitCommand extends Command
{
    public static $authorizedExtension  = ['json', 'xml'];
    public static $authorizedOutput     = ['csv', 'json', 'xml'];

    public function __construct(
        SplitterService $splitterService,
        ParameterBagInterface $params,
        string $name = null
    ) {
        $this->splitterService = $splitterService;
        $this->params = $params;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'Specify the JSON file to process.')
            ->addOption('mapping', null, InputOption::VALUE_REQUIRED, 'specify mapping class')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'specify mapping class')
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output

    ): int
    {
        $io = new SymfonyStyle($input, $output);
        $file    = $input->getOption('file');
        $io->note(sprintf('You passed an argument: %s', $file));
        $mapping = $input->getOption('mapping');
        $io->note(sprintf('You passed an argument: %s', $mapping));
        $output  = $input->getOption('output');
        $io->note(sprintf('You passed an argument: %s', $output));
        $input = pathinfo($file, PATHINFO_EXTENSION);

        if(! in_array($input, self::$authorizedExtension)) {
            throw new \InvalidArgumentException("File $file can't be processed... authorized files are: ".var_export(self::$authorizedExtension, true));
        }
        if(! file_exists($file)) {
            throw new \InvalidArgumentException("File $file does not exist.");
        }
        if(empty($this->params->get('mapping')[$mapping])) {
            throw new \InvalidArgumentException("Mapping $mapping does not exist.");
        }
        if(! in_array($output, self::$authorizedOutput)) {
            throw new \InvalidArgumentException("Output $output can't be processed... authorized output are: ".var_export(self::$authorizedExtension, true));
        }

        $results = $this->splitterService->process($file, $mapping, $input, $output);
        if(empty($results)) {
            $io->error('Errors while processing datas. please check Exception.');
            return Command::FAILURE;
        }

        $io->success('Your csv are succesfully generated in files/csv/ (teams.csv, tem_members.csv).');
        return Command::SUCCESS;
    }
}
