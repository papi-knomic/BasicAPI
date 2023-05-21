<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255|string',
            'body' => 'required|string',
            'tags' => 'required|string',
            'files' => [
                'sometimes', // the field is optional
                'array', // files should be in an array
                'max:4', // at most 4 files allowed
                function ($attribute, $value, $fail) {
                    // Check each file
                    foreach ($value as $file) {
                        if (!$file->isValid()) {
                            // The file is not valid
                            $fail('One of the uploaded files is invalid');
                        } else if (!$file->isFile()) {
                            // The input is not a file
                            $fail('One of the uploaded files is not a valid file');
                        } else if ($file->getSize() > 2 * 1024 * 1024) {
                            // The file is too large
                            $fail('One of the uploaded files is too large (max size is 2MB)');
                        } else if (strpos($file->getMimeType(), 'image') !== false && count($value) > 4) {
                            // Too many images
                            $fail('At most 4 images are allowed');
                        } else if (strpos($file->getMimeType(), 'video') !== false && count($value) > 1) {
                            // Too many videos
                            $fail('You can upload only 4 images or 1 video');
                        }
                    }
                }
            ]
        ];
    }
}
