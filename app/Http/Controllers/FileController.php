<?php

namespace App\Http\Controllers;

use App\Http\Requests\addToFavoritesRequest;
use App\Http\Requests\FilesActionRequest;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\StoreFolderRequest;
use App\Http\Requests\TrashFilesRequest;
use App\Http\Resources\FileResource;
use App\Models\File;
use App\Models\StarredFile;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use ZipArchive;

class FileController extends Controller
{
    public function myFiles(Request $request, string $folder = null): Response|ResourceCollection
    {
        // check if we are inside some dir not it the root
        if ($folder) {
            $folder = File::query()
                ->where('created_by', '=', Auth::id())
                ->where('path', '=', $folder)
                ->firstOrFail();
        } // if not just getting root
        else {
            $folder = $this->getRoot();
        }

        // getting files by user and parent dir
        $files = File::query()
            ->where('parent_id', '=', $folder->id)
            ->where('created_by', '=', Auth::id())
            ->where('deleted_at', '=', null)
            ->orderBy('is_folder', 'desc')
            ->with('starred')
            ->paginate(15);

        // correctly passing files by resource
        $files = FileResource::collection($files);

        if ($request->wantsJson()) {
            return $files;
        }
        // it's Node nested set package feature (in docs)
        // like here I am getting ancestors dirs of current dir until the root
        $ancestors = FileResource::collection([...$folder->ancestors, $folder]);

        return Inertia::render('MyFiles', compact('files', 'folder', 'ancestors'));
    }

    public function trash(): Response
    {
        $files = File::onlyTrashed()
            ->where('created_by', Auth::id())
            ->orderBy('is_folder', 'Desc')
            ->orderBy('deleted_at', 'Desc')
            ->distinct()
            ->paginate(100);
        $files = FileResource::collection($files);
        return Inertia::render('Trash', compact('files'));
    }

    public function restore(TrashFilesRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($data['all']) {
            $children = File::onlyTrashed()
                ->where('created_by', Auth::id())
                ->get();
        } else {
            $children = File::onlyTrashed()
                ->whereIn('id', $data['ids'])
                ->where('created_by', Auth::id())
                ->get();
        }
        foreach ($children as $child) {
            $child->restore();
        }
        return redirect()->back();
    }

    public function deleteForever(TrashFilesRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($data['all']) {
            $children = File::onlyTrashed()
                ->where('created_by', Auth::id())
                ->get();
        } else {
            $children = File::onlyTrashed()
                ->whereIn('id', $data['ids'])
                ->where('created_by', Auth::id())
                ->get();
        }
        foreach ($children as $child) {
            $child->deleteForever();
        }
        return redirect()->back();
    }

    public function store(StoreFileRequest $request): void
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

    private function saveFileAndAppendToNodeTree($file, int $user_id, File $parent): void
    {
        $path = $file->store('/files/' . $user_id);

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
            if (is_array($file)) {
                $folder = new File();
                $folder->is_folder = 1;
                $folder->name = $name;

                $parent->appendNode($folder);
                $this->saveFileTree($file, $folder, $user_id);
            } else {
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

    public function destroy(FilesActionRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $parentId = $request->input('parent_id'); // Get the parent_id from the request

        // Check if parent_id is provided, if not, set a default parent
        $parent = $parentId ? File::find($parentId) : $this->getRoot();

        if ($data['all']) {
            $children = $parent->children;

            foreach ($children as $child) {
                $child->moveToTrash();
            }
        } else {
            foreach ($data['ids'] ?? [] as $id) {
                $file = File::find($id);
                if ($file) {
                    $file->moveToTrash();
                }
            }
        }

        return redirect()->back();
    }


    public function download(FilesActionRequest $request): array
    {
        // getting data from request
        $data = $request->validated();
        $parentId = $request->input('parent_id'); // Get the parent_id from the request

        // Check if parent_id is provided, if not, set a default parent
        $parent = $parentId ? File::find($parentId) : $this->getRoot();
        $all = $data['all'] ?? false;
        $ids = $data['ids'] ?? [];

        if (!$all && empty($ids)) {
            return [
                'message' => 'Please select files to download'
            ];
        }

        // if we select all files in folder to delete
        if ($all) {
            $url = $this->createZip($parent->children);
            $file_name = $parent->name . '.zip';
        } else {
            // if we select 1 file or folder in storage to delete
            if (count($ids) == 1) {
                $file = File::find($ids[0]);
                // handle folder download
                if ($file->is_folder) {
                    if ($file->children->count() == 0) {
                        return [
                            'message' => 'Please select files to download'
                        ];
                    }
                    $url = $this->createZip($file->children);
                    $file_name = $file->name . '.zip';
                } else {
                    // handle file download
                    $dest = 'public/' . $file->storage_path;
                    Storage::copy($file->storage_path, $dest);

                    $url = asset(Storage::url($dest));
                    $file_name = $file->name;
                }
            } else {
                $files = File::query()->whereIn('id', $ids)->get();
                $url = $this->createZip($files);

                $file_name = $parent->name . '.zip';
            }
        }
        return [
            'url' => $url,
            'file_name' => $file_name
        ];
    }

    public function createZip($files): string
    {
        $zipPath = 'zip/' . Str::random() . '.zip';
        $publicPath = "public/$zipPath";

        if (!is_dir(dirname($publicPath))) {
            Storage::makeDirectory(dirname($publicPath));
        }

        $zipFile = Storage::path($publicPath);

        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $this->addFilesToZip($zip, $files);
        }

        $zip->close();

        return asset(Storage::url($zipPath));
    }


    private function addFilesToZip(ZipArchive $zip, $files, string $ancesstors = ''): void
    {
        foreach ($files as $file) {
            if ($file->is_folder) {
                $this->addFilesToZip($zip, $file->children, $ancesstors . $file->name . '/');
            } else {
                $zip->addFile(Storage::path($file->storage_path), $ancesstors . $file->name);
            }
        }
    }

    public function addToFavorites(addToFavoritesRequest $request): RedirectResponse
    {
        // getting data from request
        $data = $request->validated();

        $file = File::find($data['id']);

        $starredFile = StarredFile::query()
            ->where('file_id', $file->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($starredFile) $starredFile->delete();
        else {
            $data = [
                'file_id' => $file->id,
                'user_id' => Auth::id(),
                'created_at' => Carbon::now(),
            ];
            StarredFile::create($data);
        }



        return redirect()->back();
    }


}
