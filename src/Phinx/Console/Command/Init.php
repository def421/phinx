<?php

namespace Phinx\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Output\OutputInterface,
    Phinx\Registry\Registry;
    
class Init extends Command
{
    /**
     * {@inheritdoc}
     */
     protected function configure()
     {
         $this->setName('init')
              ->setDescription('Initialize the application for Phinx')
              ->addArgument('path', InputArgument::OPTIONAL, 'Which path should we initialize for Phinx?')
              ->setHelp(sprintf(
                  '%sInitializes the application for Phinx%s',
                  PHP_EOL,
                  PHP_EOL
              ));
    }

    /**
     * Initializes the application.
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // get  path to the cinfiguration config
        $path = $input->getArgument('path');

        if (null === $path) {
            $path = $this->askPath($output);
        }

        // Configuration
        $oApplicationConfig = Registry::get('configuration');
        $arrConfiguration = $oApplicationConfig->toArray();


        $fileName = $arrConfiguration['db']['configuration']['filename'];
        $filePath = $path . DIRECTORY_SEPARATOR . $fileName;

        // rewrite db config file if need
        $bWrite = true;
        $message = '<info>created</info> .' . str_replace(getcwd(), '', $filePath);
        if (file_exists($filePath)) {
            $bWrite = $this->askRewrite($output, $filePath);
            if ($bWrite) {
                $message = '<info>rewrite</info> .' . str_replace(getcwd(), '', $filePath);
            }
        }

        if ($bWrite) {
            // load the config template
            $templateDbConfig = file_get_contents(DB_CONFIG_TEMPLATE);
            // write config to user path
            if (false === file_put_contents($filePath, $templateDbConfig)) {
                throw new \RuntimeException(sprintf(
                    'The file "%s" could not be written to',
                    $path
                ));
            }

            $output->writeln($message);
        }

        //rewrite db config path in configuration
        $arrConfiguration['db']['configuration']['dir'] = str_replace(getcwd(), '', dirname($filePath));
        $oApplicationConfig->setConfiguration($arrConfiguration);
        $oApplicationConfig->save();
        Registry::set('configuration', $oApplicationConfig);


    }


    /**
     * Ask user about db configuration file
     *
     * @param $output
     * @return string
     */
    protected function askPath($output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $path = $dialog->ask(
            $output,
            "Which path should we initialize for Phinx?: \n",
            ''
        );

        if (empty($path)) {
            $path = getcwd();
        }

        if (!is_writeable($path)) {
            throw new \InvalidArgumentException(sprintf(
                'The directory "%s" is not writeable',
                $path
            ));
        }

        return realpath($path);
    }

    /**
     * Ask user aboute rewrite db configuration file
     * @param $output
     * @param $path
     * @return bool
     */
    protected function askRewrite($output, $path)
    {
        $dialog = $this->getHelperSet()->get('dialog');


        $result = $dialog->ask(
            $output,
            "file" . $path . " already exists, rewrite it? (y/n)\n",
            ''
        );

        $bRewrite = false;
        if ($result == 'y') {
            $bRewrite = true;
        }

        return $bRewrite;
    }
}