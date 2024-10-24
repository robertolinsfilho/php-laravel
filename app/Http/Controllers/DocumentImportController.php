<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessDocumentImport;
use Illuminate\Support\Facades\Storage;

class DocumentImportController extends Controller
{
    public function showImportForm()
    {
        return view('import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json',
        ]);

        $filePath = $request->file('file')->store('imports');

        // Lê o arquivo JSON
        $jsonContent = Storage::get($filePath);
        $documents = json_decode($jsonContent, true);


        foreach ($documents['documentos'] as $documentData) {
            $categoria = $documentData['categoria'] == 'Remessa' ? 1 : 2;

            // Certifique-se de que $documentData é um array
            $documentDataArray = [
                'category_id' => $categoria, // Se você precisar mapear categoria
                'title' => $documentData['titulo'],
                'contents' => $documentData['conteúdo'],
            ];

            // Despacha o job
            ProcessDocumentImport::dispatch($documentDataArray);
        }

        return back()->with('success', 'Importação iniciada com sucesso!');
    }

    public function processQueue()
    {
        return back()->with('success', 'Fila processada.');
    }
}
