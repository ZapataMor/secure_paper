<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\ProjectAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminWorkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $targetUser = $this->route('user');

        return ($user?->isAdmin() === true || $user?->isAdvisor() === true)
            && (! $this->has('draft_owner') || in_array($this->input('draft_owner'), [$user->id, (string) $user->id], true))
            && $targetUser instanceof User
            && $targetUser->isClient()
            && app(ProjectAccess::class)->allows($user, $targetUser->id);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'submission_id' => ['required', 'uuid'],
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
            'submission_id.required' => 'Actualiza el formulario antes de compartir. El mensaje se conserva.',
            'submission_id.uuid' => 'Actualiza el formulario antes de compartir. El mensaje se conserva.',
            'admin_files.*.uploaded' => 'No se pudo subir uno de los archivos. Verifica que no supere 20 MB y vuelve a intentarlo.',
            'admin_files.*.file' => 'Uno de los archivos no es valido.',
            'admin_files.*.mimes' => 'Solo puedes cargar archivos PDF, Word, Excel o imagenes.',
            'admin_files.*.max' => 'Cada archivo puede pesar maximo 20 MB.',
        ];
    }
}
