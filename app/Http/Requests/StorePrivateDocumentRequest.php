<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrivateDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isClient() === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'document_files' => ['nullable', 'array', 'max:10'],
            'document_files.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,bmp',
                'max:20480',
            ],
            'additional_information' => ['nullable', 'string', 'max:8000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document_files.*.uploaded' => 'No se pudo subir uno de los archivos. Verifica que no supere 20 MB y vuelve a intentarlo.',
            'document_files.*.file' => 'Uno de los archivos no es valido.',
            'document_files.*.mimes' => 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.',
            'document_files.*.max' => 'Cada archivo puede pesar maximo 20 MB.',
        ];
    }
}
