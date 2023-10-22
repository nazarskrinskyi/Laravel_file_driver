<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFolderRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FileController extends Controller
{
    public function myFiles(string $folder = null): \Inertia\Response
    {
        if ($folder){
            $folder = File::query()
                ->where('created_by', '=', Auth::id())
                ->where('path', '=', $folder)
                ->firstOrFail();
        }
        else $folder = $this->getRoot();
        $files = File::query()
            ->where('parent_id', '=', $folder->id)
            ->where('created_by', '=', Auth::id())
            ->where('deleted_at', '=',null)
            ->orderBy('is_folder', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $files = FileResource::collection($files);

        $ancesstors = FileResource::collection([...$folder->ancesstors, $folder]);
        return Inertia::render('MyFiles', compact('files', 'folder', 'ancesstors'));
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

        $parent->appendNode($file);

    }

    public function getRoot()
    {
       return File::query()->whereIsRoot()->where('created_by', Auth::id())->firstOrFail();
    }
}
