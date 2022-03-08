<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * Class LogController
 * @package App\Http\Controllers\Admin\Analytics
 */
class LogController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $files = glob(storage_path() . '/logs/*.log');
        $files = array_reverse($files);
        $files = array_filter($files, 'is_file');
        foreach ($files as $k => $file) {
            $disk = Storage::disk('logs');
            $file_name = basename($file);
            if ($disk->exists($file_name)) {
                $files[$k] = [
                    'file_name' => $file_name,
                    'file_size' => round((int)$disk->size($file_name) / 1048576, 2),
                    'last_modified' => date('H:i:s Y-m-d', $disk->lastModified($file_name)),
                    'path' => $file
                ];
            }
        }
        $files = array_values($files);
        return view('admin.analytics.logs.index', compact('files'));
    }

    /**
     * @param string $fileName
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function view(string $fileName)
    {
        $disk = Storage::disk('logs');
        if (!$disk->exists($fileName)) {
            abort(404);
        }
        $fileContent = $disk->get($fileName);
        return view('admin.analytics.logs.view', compact('fileName', 'fileContent'));
    }

    /**
     * @param $fileName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($fileName)
    {
        $disk = Storage::disk('logs');
        if (!$disk->exists($fileName)) {
            abort(404);
        }
        if ($disk->delete($fileName)) {
            return back()->with(['success' => 'File removed successfully']);
        }
        return back()->with(['error' => 'File removing error']);
    }
}
