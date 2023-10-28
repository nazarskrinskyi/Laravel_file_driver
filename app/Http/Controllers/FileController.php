<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use JetBrains\PhpStorm\NoReturn;

class FileController extends Controller
{
    public function myFiles(string $folder = null): \Inertia\Response
    {
        // check if we are inside some dir not it the root
        if ($folder){
            $folder = File::query()
                ->where('created_by', '=', Auth::id())
                ->where('path', '=', $folder)
                ->firstOrFail();
        }
        // if not just getting root
        else $folder = $this->getRoot();

        // getting files by user and parent dir
        $files = File::query()
            ->where('parent_id', '=', $folder->id)
            ->where('created_by', '=', Auth::id())
            ->where('deleted_at', '=',null)
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // correctly passing files by resource
        $files = FileResource::collection($files);
        // it's Node nested set package feature (in docs)
        // like here I am getting ancestors dirs of current dir until the root
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
    }

    public function storeFile(StoreFileRequest $request): void
    {
        $data = $request->validated();
        $file_tree = $request->file_tree;
        $parent = $request->parent;
        $user_id = Auth::id();

        if (!$parent) {
            $parent = $this->getRoot();
        }
        if (!empty($file_tree)) {
            $this->saveFileTree($file_tree, $parent, $user_id);
        } else {
            foreach ($data['files'] as $file) {
                $this->saveFileAndAppendToNodeTree($file, $user_id, $parent);
            }
        }
    }

    #[NoReturn] private function saveFileAndAppendToNodeTree($file, int $user_id, File $parent): void
    {
        $path = $file->store('/files/'. $user_id);

        $model = new File();
        $model->is_folder = false;
        $model->storage_path = $path;
        $model->name = $file->getClientOriginalName();
        $model->size = $file->getSize();
        $model->mime = $file->getMimeType();

        $parent->appendNode($model);
    }

    private function saveFileTree(array $file_tree, File $parent, int $user_id): void
    {
        foreach ($file_tree as $name => $file) {
            if (is_array($file)){
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user_id);
            }
            else {
                $this->saveFileAndAppendToNodeTree($file, $user_id, $parent);
            }
        }
    }
    public function createFolder(StoreFolderRequest $request): void
    {
        $data = $request->validated();
        $parentId = $request->input('parent_id'); // Get the parent_id from the request

        // Check if parent_id is provided, if not, set a default parent
        $parent = $parentId ? File::find($parentId) : $this->getRoot();

        if (!$parent) {
            // Handle the case where the parent doesn't exist (invalid parent_id)
            abort(404, 'Invalid parent folder.');
        }

        $file = new File();
        $file->is_folder = 1;
        $file->name = $data['name'];

        // adding to file system in db (nested set model package feature)
        $parent->appendNode($file);

    }

    public function getRoot()
    {
        // just getting this single root in db (parent_id === null)
       return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }
}
