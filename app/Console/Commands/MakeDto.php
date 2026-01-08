<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\File;

use Symfony\Component\Console\Input\InputOption;

class MakeDto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:dto {name} {--author=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new Dto class";

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
        $this->addOption("author", null, InputOption::VALUE_OPTIONAL, 'Isi pembuat Dto dengan format seperti contoh => --author="Nama Author <author@gmail.com>"');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name       = $this->argument("name");
        $DtoPath = app_path("Dto/" . $name . ".php");
        $flagAuthor = $this->option("author") ?? null;

        if (File::exists($DtoPath)) :
            $this->error("Dto $name sudah ada!");
            return;
        endif;

        // Buat direktori jika belum ada
        $directory = pathinfo($DtoPath, PATHINFO_DIRNAME);
        if (!File::isDirectory($directory)) :
            File::makeDirectory($directory, 0755, true);
        endif;

        File::put($DtoPath, $this->isiClass($name, $flagAuthor));

        $this->info("Dto berhasil dibuat: " . $name);
    }

    private function isiClass($name, $flagAuthor)
    {
        if (!empty($flagAuthor)) :
            $author = "* @author\t" . $flagAuthor . "\n * ";
        else :
            $author = " * ";
        endif;

        $segments   = explode("/", $name);
        $namaDto = end($segments);
        $namespace  = ($segments ? "\\" . implode("\\", array_slice($segments, 0, -1)) : "");

        return  "<?php

namespace App\Dto" . $namespace . ";

/**
 * Class " . $namaDto . "
 *
 * @package\tApp\Dto
 $author
 */
class " . $namaDto . "
{

    public function __construct()
    {
    }

}
";
    }
}
