<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    public function index()
    {
        return view('swagger');
    }

    public function swaggerJson()
    {
        $user = auth()->user();
        $dockPath = 'swagger/api.json';
        if ($user->isSuperAdmin()) {
            $dockPath = 'swagger/api_admin.json';
        }

        return response(file_get_contents(resource_path($dockPath)));
    }

    public function swaggerYaml()
    {
        $dockPath = 'swagger/api.yaml';

        return response(file_get_contents(resource_path($dockPath)));
    }
}
