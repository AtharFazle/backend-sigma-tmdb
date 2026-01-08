<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;

use Symfony\Component\Console\Input\InputOption;

class MakeHelper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:helper {name} {--author=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new helper class";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function configure()
    {
        $this->addOption("author", null, InputOption::VALUE_OPTIONAL, 'Isi pembuat helper dengan format seperti contoh => --author="Nama Author <author@gmail.com>"');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name       = $this->argument("name");
        $helperPath = app_path("Helpers/" . $name . ".php");
        $flagAuthor = $this->option("author") ?? null;

        if (File::exists($helperPath)) :
            $this->error("Helper $name sudah ada!");
            return;
        endif;

        // Buat direktori jika belum ada
        $directory = pathinfo($helperPath, PATHINFO_DIRNAME);
        if (!File::isDirectory($directory)) :
            File::makeDirectory($directory, 0755, true);
        endif;

        File::put($helperPath, $this->isiClass($name, $flagAuthor));

        $this->info("Helper berhasil dibuat: " . $name);
    }

    private function isiClass($name, $flagAuthor)
    {
        if (!empty($flagAuthor)) :
            $author = "* @author\t" . $flagAuthor . "\n * ";
        else :
            $author = " * ";
        endif;

        $segments   = explode("/", $name);
        $namaHelper = end($segments);
        $namespace  = ($segments ? "\\" . implode("\\", array_slice($segments, 0, -1)) : "");

        return  "<?php

namespace App\Helpers" . $namespace . ";

use App\Traits\GlobalTrait;

/**
 * Class " . $namaHelper . "
 *
 * @package\tApp\Helpers
 $author
 */
class " . $namaHelper . "
{
    use GlobalTrait;

    public function __construct()
    {
    }

    // Tulis function helper disini
}
";
    }
}
