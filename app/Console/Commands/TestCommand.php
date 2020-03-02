<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

            $hasManyModels = $this->getModelsCollection($hasManyRelations);

//            $hasManyFactories = $this->generateHasManyFactories($hasManyModels);
//            $hasManyFields = $this->generateHasManyFields($hasManyModels);
//            $hasManyMethods = $this->generateHasManyMethods($hasManyModels);

            $hasManyStub = $this->generateStubsByDummyString($this->hasManyStubs(), 'DummyHasMany', $hasManyModels);

            $stub = str_replace('//dummyHasManyFactory', $hasManyStub->get(0), $stub);
            $stub = str_replace('//dummyHasManyFields', $hasManyStub->get(1), $stub);
            $stub = str_replace('//dummyHasManyMethods', $hasManyStub->get(2), $stub);


            $belongsRelations = $this->ask('belongsTo relation classes?');

            $belongsToModels = $this->getModelsCollection($belongsRelations);

            $belongsToStub = $this->generateStubsByDummyString($this->belongsToStubs(), 'DummyBelongsTo', $belongsToModels);

            $stub = str_replace('//dummyBelongsToFactory', $belongsToStub->get(0), $stub);
            $stub = str_replace('//dummyBelongsToFields', $belongsToStub->get(1), $stub);
            $stub = str_replace('//dummyBelongsToMethods', $belongsToStub->get(2), $stub);
            $stub = str_replace('dummy-belongs-to-relation', Str::lower( $this->argument('modelName')), $stub);

            $this->createFactories($hasManyModels);
            $this->createFactories($belongsToModels);


            $hasManyUses = $this->generateUseStatements($hasManyModels);
            $belongsToUses = $this->generateUseStatements($belongsToModels);


            $allUses = collect([$hasManyUses, $belongsToUses])->implode("\n");

            $stub = str_replace('//dummyUse', $allUses, $stub);

        }

        if (File::exists($path . "\\$name.php")) {
            $this->info('File already exists');
            return false;
        }

        File::makeDirectory($path);

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


    protected function getStubFile($type = 'test-factory.stub')
    {
        return File::get(app_path('stubs') . "\\$type");
    }


    protected function generateStubsByDummyString(array $stubsArr, string $dummyString, Collection $models)
    {

        return collect($stubsArr)->map(function ($stub) use ($dummyString, $models) {
            $stub = $this->getStubFile($stub);

            return $models->map(function ($m) use ($dummyString, $stub) {
                return str_replace($dummyString, $m['model'], $stub);
            })->implode("\n \n");
        });

    }

    protected function generateUseStatements(Collection $models)
    {
        return $models->pluck('nameSpace')
            ->map(function ($use) {
                return "use $use;";
            })
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

    /**
     * @param $relations
     * @return Collection
     */
    public function getModelsCollection($relations): Collection
    {
        return collect(explode(',', $relations))->filter()->mapWithKeys(function ($modelName, $key) {
            return [$key => ['model' => $modelName, 'nameSpace' => $this->getModel($modelName)]];
        });
    }


    protected function hasManyStubs()
    {
        return [
            '\has-many\has-many.stub',
            '\has-many\has-many-field.stub',
            '\has-many\has-many-method.stub',
        ];
    }

    protected function belongsToStubs()
    {
        return [
            '\belongs-to\belongs-to.stub',
            '\belongs-to\belongs-to-field.stub',
            '\belongs-to\belongs-to-method.stub',
        ];
    }


}
