<?php namespace PiekJ\B302Authentication;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PublishCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'b302-auth:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all the needed configs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $config = $this->option('config');
        $view = $this->option('view');

        if ($config)
        {
            $this->info('Publishing configs');
            $this->call('config:publish', ['zizaco/confide' => true]);
            $this->call('config:publish', ['zizaco/entrust' => true]);
            $this->call('config:publish', ['piek-j/b302-authentication' => true]);
        }

        if ($view)
        {
            $this->info('Publishing views');
            $this->call('view:publish', ['zizaco/confide' => true]);
            $this->call('view:publish', ['piek-j/b302-authentication' => true]);
        }

        if (!$config && !$view)
        {
            $this->info('Nothing to publish');
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('config', null, InputOption::VALUE_NONE, 'Publish all the configs'),
            array('view', null, InputOption::VALUE_NONE, 'Publish all the views'),
        );
    }

}
