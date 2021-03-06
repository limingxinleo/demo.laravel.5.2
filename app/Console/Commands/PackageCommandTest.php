<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PackageCommandTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:limx-package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'package the program';

    /**
     * 配置文件的名称.
     *
     * @var string
     */
    protected $file_name = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $root = config_path('data');
        if (!is_dir($root)) {
            mkdir($root, 0755, true);
        }
        $this->file_name = $root . '/package.php';
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $app = $this->getConfig();
        if ($app) {
            \limx\func\File::copy($app['root'], $app['files'], $app['dst']);
            \limx\func\File::zip(dirname($app['dst']), basename($app['dst']), dirname($app['dst']));
        }
    }

    private function isConfig()
    {
        if (file_exists($this->file_name)) {
            return true;
        }
        return false;
    }

    /**
     * [getConfig 获取配置文件]
     * @author limx
     * @return bool|mixed
     */
    private function getConfig()
    {
        if ($this->isConfig()) {
            return include $this->file_name;
        } else {
            $this->createConfig();
            $this->error('please set your package config');
        }
        return false;
    }

    /**
     * [createConfig 新建配置文件]
     * @author limx
     * @return bool|string
     */
    private function createConfig()
    {
        $content = '<?php return [
    \'root\' => \'\',
    \'files\' => [

    ],
    \'dst\' => \'\',
];';
        if (file_exists($this->file_name)) {
            $this->error($this->file_name . ' is exists!');
            return false;
        }
        file_put_contents($this->file_name, $content);
        $this->error('package config is create success!');
        return true;
    }
}
