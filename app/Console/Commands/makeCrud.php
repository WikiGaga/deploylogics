<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use File;
class makeCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crud completed';

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
    public function handle()
    {
        $view = $this->argument('view');

        $path = $this->viewPath($view);

        $this->createDir($path);

        if (File::exists($path))
        {
            $this->error("File {$path} already exists!");
            return;
        }
        // $custom_view = view('crud.form');

        File::copy('http://127.0.0.1:8000/crud/form.blade.php',$path);

        $this->info("File {$path} created.");
    }
    /**
     * Get the view full path.
     *
     * @param string $view
     *
     * @return string
     */
    public function viewPath($view)
    {
        $view = str_replace('.', '/', $view) . '.blade.php';

        $path = "resources/views/{$view}";

        return $path;
    }
    public function jsPath($js)
    {
        $js = str_replace('.', '/', $js) . '.js';

        $path = "public/js/pages/{$js}";

        return $path;
    }

    /**
     * Create view directory if not exists.
     *
     * @param $path
     */
    public function createDir($path)
    {
        $dir = dirname($path);

        if (!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
    }
}
