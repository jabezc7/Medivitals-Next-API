<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SpinUpPack extends Command
{
    protected $signature = 'spinup:pack';

    protected $description = 'Generate all the files for a new Data Model';

    private string $modelSingle;
    private string $modelPlural;
    private string $table;

    public function __construct()
    {
        parent::__construct();
        $this->modelSingle = '';
        $this->modelPlural = '';
        $this->table = '';
    }

    public function handle(): void
    {
        $this->modelSingle = $this->ask('Singular Model Name');
        $this->modelPlural = $this->ask('Plural Model Name', $this->modelSingle.'s');
        $this->table = $this->ask('Table Name', strtolower($this->modelPlural));

        // Controller
        $controllerStub = File::get(resource_path('stubs/Controller.stub'));
        File::put(app_path('Http/Controllers/Api/'.$this->modelPlural.'Controller.php'), $this->replaceTags($controllerStub));

        // Model
        $modelStub = File::get(resource_path('stubs/Model.stub'));
        File::put(app_path('Models/'.$this->modelSingle.'.php'), $this->replaceTags($modelStub));

        // Resource
        if (!File::isDirectory(app_path('Http/Resources/'.$this->modelSingle))){
            File::makeDirectory(app_path('Http/Resources/'.$this->modelSingle));
        }

        $resourceStub = File::get(resource_path('stubs/ResourceResource.stub'));
        File::put(app_path('Http/Resources/'.$this->modelSingle.'/'.$this->modelSingle.'Resource.php'), $this->replaceTags($resourceStub));

        $collectionStub = File::get(resource_path('stubs/ResourceCollection.stub'));
        File::put(app_path('Http/Resources/'.$this->modelSingle.'/'.$this->modelSingle.'Collection.php'), $this->replaceTags($collectionStub));

        // Filter
        $filterStub = File::get(resource_path('stubs/Filter.stub'));
        File::put(app_path('Http/Filters/'.$this->modelSingle.'Filter.php'), $this->replaceTags($filterStub));

        // Policy
        $policyStub = File::get(resource_path('stubs/Policy.stub'));
        File::put(app_path('Policies/'.$this->modelSingle.'Policy.php'), $this->replaceTags($policyStub));

        // Migration
        $migrationStub = File::get(resource_path('stubs/Migration.stub'));
        File::put(database_path('migrations/'.Carbon::now()->format('Y_m_d_u').'_create_'.strtolower(Str::snake($this->table)).'_table.php'), $this->replaceTags($migrationStub));

        // Route
        $apiRoutesFile = File::get(base_path('routes/api.php'));
        $apiRoutesFile = str_replace(
            [
                '/* INSERT ROUTES HERE */',
                '/* INSERT USE HERE */'
            ],
            [
                "// ".$this->modelPlural."\r\n\t\tRoute::resource('".strtolower($this->modelPlural)."', ".$this->modelPlural."Controller::class);\r\n\r\n\t\t/* INSERT ROUTES HERE */",
                "use App\Http\Controllers\Api\\".$this->modelPlural."Controller;\r\n/* INSERT USE HERE */"
            ],
            $apiRoutesFile
        );

        File::put(base_path('routes/api.php'), $apiRoutesFile);

        // Policy Auth Provider
        $authServiceProviderFile = File::get(app_path('Providers/AuthServiceProvider.php'));
        $authServiceProviderFile = str_replace(
            [
                '/* INSERT POLICY HERE */',
                '/* INSERT POLICY USE HERE */',
                '/* INSERT MODEL USE HERE */',
            ],
            [
                $this->modelSingle."::class => ".$this->modelSingle."Policy::class,\r\n\t\t/* INSERT POLICY HERE */",
                "use App\Policies\\".$this->modelSingle."Policy;\r\n/* INSERT POLICY USE HERE */",
                "use App\Models\\".$this->modelSingle.";\r\n/* INSERT MODEL USE HERE */"
            ],
            $authServiceProviderFile
        );

        File::put(app_path('Providers/AuthServiceProvider.php'), $authServiceProviderFile);
    }

    public function replaceTags($content): array|string
    {
        return str_replace(
            [
                '{{ SingularModelName }}',
                '{{ SingularModelNameLower }}',
                '{{ PluralModelName }}',
                '{{ PluralModelNameLower }}',
                '{{ TableNameCamelCase }}',
                '{{ TableNameLower }}'
            ],
            [
                $this->modelSingle,
                strtolower($this->modelSingle),
                $this->modelPlural,
                strtolower($this->modelPlural),
                ucfirst(Str::camel($this->table)),
                strtolower($this->table),
            ],
            $content
        );
    }
}
