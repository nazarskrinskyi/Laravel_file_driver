<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class StoreFileRequest extends ParentIdBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $paths = array_filter($this->relative_paths ?? [], fn($file) => $file != null);

        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths)
        ]);
    }

    private function detectFolderName(array $paths): ?string
    {
        if (!$paths) {
            return null;
        }

        $parts = explode('/', $paths[0]);
        return $parts[0];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if (!$this->folder_name) {
                        /** @var $value UploadedFile */
                        $file = File::query()->where('name', $value->getClientOriginalName())
                            ->where('parent_id', $this->parent_id)
                            ->where('created_by', Auth::id())
                            ->whereNull('deleted_at')
                            ->exists();
                        if ($file) {
                            $fail('File - "' . $value->getClientOriginalName() . '" already exists!');
                        }
                    }
                }
            ],

            'folder_name' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        /** @var $value UploadedFile */
                        $file = File::query()->where('name', $value)
                            ->where('parent_id', $this->parent_id)
                            ->where('created_by', Auth::id())
                            ->whereNull('deleted_at')
                            ->exists();
                        if ($file) {
                            $fail('Folder - "' . $value . '" already exists!');
                        }
                    }
                }
            ],
        ]);
    }
}
