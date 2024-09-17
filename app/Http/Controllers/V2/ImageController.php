<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class ImageController extends Controller
{
    private static $jsonData, $myId, $roleId, $unitId, $token;

    public function display(string $folder, Request $request)
    {
        $fileName = $request->query('foto');
        $fileType = $request->query('ext');

        $fullFileName = $fileName . '.' . $fileType;

        if ($folder == 'profile') {
            // SERVER STORAGE
            // $fileLocation = env('AWS_BUCKET').'/Profile/' . $fullFileName;
            // END
            $fileLocation = env('IMAGE_BASE_PATH') . 'Profile/' . $fullFileName;
        }

        if ($folder == 'ktp') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'KTP/' . $fullFileName;
        }

        if ($folder == 'nira_ppni') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'NIRA_PPNI/' . $fullFileName;
        }

        if ($folder == 'kta_inwocna') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'KTA_Inwocna/' . $fullFileName;
        }

        if ($folder == 'str') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'STR/' . $fullFileName;
        }

        if ($folder == 'ijazah') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'Ijazah/' . $fullFileName;
        }

        if ($folder == 'ijazah_profesi') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'Ijazah_Profesi/' . $fullFileName;
        }

        if ($folder == 'transkrip') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'Transkrip/' . $fullFileName;
        }

        if ($folder == 'lampiran') {
            $fileLocation = env('IMAGE_BASE_PATH') . 'Lampiran/' . $fullFileName;
        }

        switch ($folder) {
            case 'profile':
                // SERVER STORAGE
                // if (Storage::disk('s3')->exists('Profile/'.$fullFileName)) {
                //     $x = Storage::disk('s3')->get('Profile/'.$fullFileName);

                //     header("Content-Type: ".Storage::disk('s3')->mimeType('Profile/'.$fullFileName));
                //     header("Content-Length: " . Storage::disk('s3')->size('Profile/'.$fullFileName));

                //     return Storage::disk('s3')->response('Profile/'.$fullFileName);
                // } else {
                //     throw new \Exception("Error Processing Request", 500);
                // }
                // END
                $openFile = fopen($fileLocation, 'r');
                // Check if file exists
                if (!$openFile) {
                    $mimeType = null;

                    return null;
                } else {
                    $mimeType = mime_content_type($openFile);
                    // send the right headers
                    header("Content-Type: " . $mimeType);
                    header("Content-Length: " . filesize($fileLocation));
                    // dump the picture and stop the script
                    fpassthru($openFile);
                    exit;
                }
                break;

            default:
                if (file_exists($fileLocation)) {
                    $openFile = fopen($fileLocation, 'r');

                    $mimeType = mime_content_type($openFile);

                    // send the right headers
                    header("Content-Type: " . $mimeType);
                    header("Content-Length: " . filesize($fileLocation));

                    // dump the picture and stop the script
                    fpassthru($openFile);
                    exit;
                } else {
                    $mimeType = null;

                    return null;
                }
                break;
        }
    }
}
