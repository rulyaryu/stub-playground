<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test-factory-class:generate {modelName=Model} {--relations}';

    private $namespace = 'App';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $className = $this->getModel($this->argument('modelName'));
        $model = new $className();

//        dd(__DIR__);

        $name = "{$this->argument('modelName')}Factory";

        $path = base_path("tests\Setup");

        $stub = $this->getStubFile();

        if ($this->option('relations')) {


            $hasManyRelations = $this->ask('hasMany relation classes?');

            $hasManyModels = collect(explode(',', $hasManyRelations))->filter()->mapWithKeys(function ($modelName, $key) {
                return [$key => ['model' => $modelName, 'nameSpace' => $this->getModel($modelName)]];
            });

            $hasManyFactories = $this->generateHasManyFactories($hasManyModels);
            $hasManyFields = $this->generateHasManyFields($hasManyModels);
            $hasManyUses = $this->generateUseStatements($hasManyModels);


            $stub = str_replace('//dummyFactory', $hasManyFactories, $stub);
            $stub = str_replace('//dummyFields', $hasManyFields, $stub);
            $stub = str_replace('//dummyUse', $hasManyUses, $stub);


            $this->createFactories($hasManyModels);

//            dd(__DIR__);
        }



        File::makeDirectory($path);

        //dummyFields
        //dummyMethods
        //dummyFactory
        //dummyUse

        File::put($path . "\\$name.php", str_replace('DummyClass', $name, $stub));

        $this->info(' created successfully.');

        $this->callSilent('make:factory', [
            'name' => $name
        ]);

        return true;
    }

    private function getModel($modelName = 'Model')
    {
        return "{$this->namespace}\\$modelName";
    }
    // private function getModel($modelName = 'Model')
    // {
    //     return "{$this->namespace}\\$modelName";
    // }


    protected function buildClass($name)
    {

    }


    protected function getStubFile($type = 'test-factory.stub')
    {
        return File::get(app_path('stubs') . "\\$type");
    }

    protected function generateHasManyFactories(Collection $hasManyModels)
    {
        $stub = $this->getStubFile('has-many.stub');
        return $hasManyModels->map(function ($m) use ($stub) {
             return str_replace('DummyHasMany', $m['model'], $stub);
        })->implode("\n \n");
    }

    protected function generateHasManyFields(Collection $hasManyModels)
    {
        $stub = $this->getStubFile('has-many-field.stub');
        return $hasManyModels->map(function ($m) use ($stub) {
            return str_replace('DummyHasMany', $m['model'], $stub);
        })->implode("\n \n");
    }

    protected function generateUseStatements(Collection $hasManyModels) {
        return $hasManyModels->pluck('nameSpace')
            ->map(function ($use) { return "use $use;";})
            ->implode("\n \n");
    }

    private function createFactories(Collection $models)
    {
        $models->each(function ($m) {
            $this->callSilent('make:factory', [
                'name' => "{$m['model']}Factory",
                '-m' => $m['model']
            ]);
        });
    }


}
