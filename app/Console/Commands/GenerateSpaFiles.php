<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateSpaFiles extends Command
{
    protected $signature = 'generate:spa-files';

    protected $description = 'Generate SPA Boilerplate Files';

    private string $modelSingle;
    private string $modelPlural;
    private string $slug;

    public function __construct()
    {
        parent::__construct();
        $this->modelSingle = '';
        $this->modelPlural = '';
        $this->slug = '';
    }

    public function handle(): int
    {
        $this->modelSingle = $this->ask('Singular Model Name');
        $this->modelPlural = $this->ask('Plural Model Name', $this->modelSingle.'s');
        $this->slug = $this->ask('Slug', strtolower($this->modelPlural));

        try {
            File::makeDirectory(base_path('tmp/' . $this->slug), 0775, true);
        } catch(Exception $e){
            // DO Nothing
        }

        $listViewStub = File::get(resource_path('stubs/SpaListView.stub'));
        File::put(base_path('tmp/' . $this->slug . '/' . $this->modelPlural.'.vue'), $this->replaceTags($listViewStub));

        $formViewStub = File::get(resource_path('stubs/SpaFormView.stub'));
        File::put(base_path('tmp/' . $this->slug . '/Form.vue'), $this->replaceTags($formViewStub));

        $viewStub = File::get(resource_path('stubs/SpaView.stub'));
        File::put(base_path('tmp/' . $this->slug . '/' . $this->modelSingle . '.vue'), $this->replaceTags($viewStub));

        $viewStub = File::get(resource_path('stubs/SpaRouteJson.stub'));
        File::put(base_path('tmp/' . $this->slug . '/route.json'), $this->replaceTags($viewStub));

        return 0;
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
                ucfirst(Str::camel($this->slug)),
                strtolower($this->slug),
            ],
            $content
        );
    }
}
