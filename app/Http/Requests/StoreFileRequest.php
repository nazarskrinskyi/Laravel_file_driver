<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

/**
 * @property mixed $file_tree
 * @property array $file_paths
 * @property string $folder_name
 * @property int $parent_id
 */
class StoreFileRequest extends ParentIdBaseRequest
{

    // function that detect folders name and paths to files
    protected function prepareForValidation(): void
    {
        $paths = array_filter($this->relative_paths ?? [], fn($file) => $file != null);

        $this->merge([
            'file_paths' => $paths,
            'folder_name' => $this->detectFolderName($paths)
        ]);
    }

    // function that detect folders name and paths to files
    protected function passedValidation(): void
    {
        $data = $this->validated();
        $this->replace([
            'file_tree' => $this->buildFileTree($this->file_paths, $data['files'])
        ]);
    }

    // function that gets main folder name
    private function detectFolderName(array $paths): ?string
    {
        if (!$paths) {
            return null;
        }

        $parts = explode('/', $paths[0]);
        return $parts[0];
    }

    // function that save folder existence inside another folders
    // And creates folder/file hierarchy
    private function buildFileTree(array $paths, array $files)
    {

        $paths = array_slice($paths, 0, count($files));

        $node_tree = [];

        foreach ($paths as $key => $path) {
            // here we are breaking into array our folders and files
            $parts = explode('/', $path);

            // creating reference on our tree that will allow us to move in hierarchy tree
            $tree = &$node_tree;

            foreach ($parts as $i => $part) {
                // here we assign folder as key in array
                if (!isset($tree[$part])) {
                    $tree[$part] = [];
                }
                // here we're finding last element in the path (file)
                // here we're stopping and passing to current part(directory) this file
                if ($i === count($parts) - 1) {
                    $tree[$part] = $files[$key];
                } else {
                    // here if it's not file then we're going back and doing process again
                    $tree = &$tree[$part];
                }
            }
        }

        return $node_tree;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                // callback func that check file existence before upload
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
                // callback func that check folder existence before upload
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
