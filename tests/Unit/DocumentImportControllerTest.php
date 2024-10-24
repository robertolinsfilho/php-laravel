<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessDocumentImport;

class DocumentImportControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Fake the storage
        Storage::fake();
        // Fake the queue
        Queue::fake();
    }

    public function testShowImportForm()
    {
        $response = $this->get('/import');
        $response->assertStatus(200);
        $response->assertViewIs('import');
    }

    public function testImportValidFile()
    {
             // Mockando o Storage
        Storage::fake('imports');

             // Criando um arquivo JSON de teste
        $jsonData = [
            'documentos' => [
                [
                    'titulo' => 'Documento 1',
                    'conteúdo' => 'Conteúdo do documento 1',
                    'categoria' => 'Remessa'
                ],
                [
                    'titulo' => 'Documento 2',
                    'conteúdo' => 'Conteúdo do documento 2',
                    'categoria' => 'Outro'
                ],
            ],
        ];

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test.json', json_encode($jsonData));

             // Asserte que o job será despachado
        Queue::fake();

             // Enviando a requisição para o endpoint de importação
        $response = $this->post('/import', [
            'file' => $file,
        ]);

        // Verificando se a resposta é um redirecionamento
        $response->assertRedirect();

        // Verificando se a sessão contém a mensagem de sucesso
        $response->assertSessionHas('success', 'Importação iniciada com sucesso!');

        // Asserte que o job foi despachado duas vezes
        Queue::assertPushed(ProcessDocumentImport::class, 2);

        // Verifique se os dados estão corretos
        $this->assertCount(2, Queue::pushed(ProcessDocumentImport::class));
    }

    public function testImportValidFileCount()
    {
             // Mockando o Storage
        Storage::fake('imports');

             // Criando um arquivo JSON de teste
        $jsonData = [
            'documentos' => [
                [
                    'titulo' => 'Documento 1',
                    'conteúdo' => 'Conteúdo do documento 1',
                    'categoria' => '1'
                ],
                [
                    'titulo' => 'Documento 2',
                    'conteúdo' => 'Conteúdo do documento 2',
                    'categoria' => '2'
                ],
            ],
        ];

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test.json', json_encode($jsonData));

             // Asserte que o job será despachado
        Queue::fake();

             // Enviando a requisição para o endpoint de importação
        $response = $this->post('/import', [
            'file' => $file,
        ]);

        // Verificando se a resposta é um redirecionamento
        $response->assertRedirect();

        $this->assertLessThan(255, strlen($jsonData['documentos'][0]['conteúdo']));
        // Verificando se a sessão contém a mensagem de sucesso
        $response->assertSessionHas('success', 'Importação iniciada com sucesso!');

        // Asserte que o job foi despachado duas vezes
        Queue::assertPushed(ProcessDocumentImport::class, 2);

        // Verifique se os dados estão corretos
        $this->assertCount(2, Queue::pushed(ProcessDocumentImport::class));
    }

    public function testImportValidTitle()
    {
             // Mockando o Storage
        Storage::fake('imports');

             // Criando um arquivo JSON de teste
        $jsonData = [
            'documentos' => [
                [
                    'titulo' => 'janeiro teste teste teste',
                    'conteúdo' => 'teste',
                    'categoria' => 'Remessa Parcial'
                ],
                [
                    'titulo' => 'semestre teste teste teste',
                    'conteúdo' => 'teste',
                    'categoria' => 'Remessa'
                ],
            ],
        ];

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('test.json', json_encode($jsonData));

             // Asserte que o job será despachado
        Queue::fake();

             // Enviando a requisição para o endpoint de importação
        $response = $this->post('/import', [
            'file' => $file,
        ]);

        // Verificando se a resposta é um redirecionamento
        $response->assertRedirect();

        foreach($jsonData as $documentos){
            if($documentos[0]['categoria'] == 'Remessa Parcial'){
                $this->assertStringContainsString("janeiro", $documentos[0]['titulo']);
            }
            if($documentos[1]['categoria'] == 'Remessa'){
                $this->assertStringContainsString("semestre", $documentos[1]['titulo']);
            }
        }
        // Verificando se a sessão contém a mensagem de sucesso
    }
    public function testImportInvalidFile()
    {
        $response = $this->post('/import', [
            'file' => null,
        ]);

        $response->assertSessionHasErrors(['file']);
    }
}
