<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ZipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function downloadZip()
    // {
    //     $zip = new ZipArchive;

    //     $fileName = 'myNewFile.zip';

    //     if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE) {
    //         $files = File::files(public_path('myFiles'));

    //         foreach ($files as $key => $value) {
    //             $relativeNameInZipFile = basename($value);
    //             $zip->addFile($value, $relativeNameInZipFile);
    //         }

    //         $zip->close();
    //     }

    //     return response()->download(public_path($fileName));
    // }

    public function downloadZip()
    {
        $fileName = 'myNewFile.zip';

        $filePath = public_path($fileName);
        $zip = new \ZipArchive();

        if ($zip->open($filePath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot open ' . $filePath);
        }

        $this->addContent($zip, public_path('myFiles'));
        $zip->close();
    }

    private function addContent(\ZipArchive $zip, string $path)
    {
        /** @var SplFileInfo[] $files */
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::FOLLOW_SYMLINKS
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $filePath = $iterator->getPathName();
                $relativePath = substr($filePath, strlen($path) + 1);

                if (!$iterator->isDir()) {
                    $zip->addFile($filePath, $relativePath);
                } else {
                    if ($relativePath !== false) {
                        $zip->addEmptyDir($relativePath);
                    }
                }
            }
            $iterator->next();
        }
    }
}
