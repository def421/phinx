<?php

namespace Phinx\Console\Command;

use Symfony\Component\Config\FileLocator,
    Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Output\OutputInterface,
    Phinx\Config\Config,
    Phinx\Migration\Manager,
    Phinx\Adapter\AdapterInterface,
    Phinx\Registry\Registry,
    Phinx\Config\ApplicationConfig;

/**
 * Abstract command, contains bootstrapping info
 *
 * @author Rob Morgan <robbym@gmail.com>
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var ArrayAccess
     */
    protected $config;
    
    /**
     * @var \Phinx\Adapter\AdapterInterface
     */
    protected $adapter;
    
    /**
     * @var \Phinx\Migration\Manager;
     */
    protected $manager;
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('--configuration', '-c', InputArgument::OPTIONAL, 'The configuration file to load');
    }
    
    /**
     * Bootstrap Phinx.
     *
     * @return void
     */
    public function bootstrap(InputInterface $input, OutputInterface $output)
    {
        $oApplicationConfig = Registry::get('configuration');
        $arrConfiguration = $oApplicationConfig->toArray();

        $path = $arrConfiguration['db']['configuration']['dir'];
        $fileName = $arrConfiguration['db']['configuration']['filename'];
        $configFilePath = $path . DIRECTORY_SEPARATOR . $fileName;

        // if reconfigure
        $configFile = $input->getOption('configuration');
        if (null !== $configFile) {
            $cwd = getcwd();

            // locate the phinx config file (default: phinx.yml)
            // TODO - In future walk the tree in reverse (max 10 levels)
            $locator = new FileLocator(array(
                $cwd . DIRECTORY_SEPARATOR
            ));

            // Locate() throws an exception if the file does not exist
            $configFilePath = $locator->locate($configFile, $cwd, $first = true);
            $oApplicationConfig = new ApplicationConfig($configFilePath);
            Registry::set('configuration', $oApplicationConfig);
        }


        $output->writeln('<info>using config</info> .' . str_replace(getcwd(), '', realpath($configFilePath)));
        
        // parse the config file and load it into the config object
        $this->setConfig(Config::fromYaml($configFilePath));

        // report the migrations path
        $output->writeln('<info>using migration path</info> ' . $this->getConfig()->getMigrationPath());
    
        if (null === $this->getManager()) {
            // load the migrations manager and inject the config
            $manager = new Manager($this->getConfig(), $output);
            $this->setManager($manager);
        }
    }

    /**
     * Sets the config.
     *
     * @param \ArrayAccess $config
     * @return AbstractCommand
     */
    public function setConfig(\ArrayAccess $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * Gets the config.
     *
     * @return \ArrayAccess
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Sets the database adapter.
     *
     * @param AdapterInterface $adapter
     * @return AbstractCommand
     */
    public function setAdapter(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * Gets the database adapter.
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Sets the migration manager.
     *
     * @param Manager $manager
     * @return AbstractCommand
     */
    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
        return $this;
    }
    
    /**
     * Gets the migration manager.
     *
     * @return \Manager
     */
    public function getManager()
    {
        return $this->manager;
    }
}