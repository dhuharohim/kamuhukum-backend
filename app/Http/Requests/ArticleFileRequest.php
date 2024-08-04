<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'uuid' => 'required',
            'article_files' => 'required|array|min:1',
            'article_files.*.name' => 'required',
            'article_files.*.file' => 'required|file|mimes:docx,pdf,xlsx,xls,jpg,jpeg|max:10240',
            'article_files.*.type' => 'required|string|in:Article Text,Plagiarism Report,Research Instrument,Research Materials,Research Result,Transcripts,Data Analysis,Data Set,Source Texts,Other'
        ];
    }

    public function messages()
    {
        return [
            'article_files.required' => 'At least one file is required.',
            'article_files.array' => 'The files must be an array.',
            'article_files.*.file.required' => 'Each file is required.',
            'article_files.*.file.file' => 'Each file must be a valid file.',
            'article_files.*.file.mimes' => 'Each file must be a file of type: docx, pdf, xlsx, xls, jpg, jpeg.',
            'article_files.*.file.max' => 'Each file must not be greater than 10MB.',
            'article_files.*.type.required' => 'The type field is required.',
            'article_files.*.type.in' => 'The type must be one of the following: Article Text, Plagiarism Report, Research Instrument, Research Materials, Research Result, Transcripts, Data Analysis, Data Set, Source Texts, Other.'
        ];
    }
}
