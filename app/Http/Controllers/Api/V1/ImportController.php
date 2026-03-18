<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\XmlImportService;

class ImportController extends Controller
{
    public function __construct(private XmlImportService $xmlImportService) {}

    public function store()
    {
        $this->xmlImportService->importAllXmlFiles();
        return response()->json(['message' => 'Importação realizada com sucesso'], 200);
    }
}