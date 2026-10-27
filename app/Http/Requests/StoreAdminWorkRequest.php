<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $targetUser = $this->route('user');

        return ($user?->isAdmin() === true || $user?->isAdvisor() === true)
            && $targetUser instanceof User
            && $targetUser->isClient();
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'admin_message' => ['nullable', 'string', 'max:8000'],
            'admin_files' => ['nullable', 'array', 'max:10'],
            'admin_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,bmp', 'max:20480'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'admin_files.*.uploaded' => 'No se pudo subir uno de los archivos. Verifica que no supere 20 MB y vuelve a intentarlo.',
            'admin_files.*.file' => 'Uno de los archivos no es valido.',
            'admin_files.*.mimes' => 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.',
            'admin_files.*.max' => 'Cada archivo puede pesar maximo 20 MB.',
        ];
    }
}
