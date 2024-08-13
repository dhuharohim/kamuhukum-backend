<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleBackboneRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:incomplete,submission,review,production',
            'title' => 'required',
            'section' => 'required|in:article,general_article',
            'subtitle' => 'nullable',
            'slug' => 'nullable',
            'abstract' => 'required',
            'keywords' => 'nullable',
            'article_files' => 'required|array|min:1',
            'contributors' => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'contributors.required' => 'At least one contributor',
            'contributors.*.given_name' => 'Contributor must have a first name',
            'contributors.*.family_name' => 'Contributor must have a last name',
            'contributors.*.affilation' => 'Contributor must have a affiliation',
            'contributors.*.country' => 'Contributor must have a country',
            'contributors.*.role' => 'Contributor must have a role',
            'article_files.required' => 'At least one file is required.',
            'article_files.*.file.required' => 'Each file is required.',
            'article_files.*.type.required' => 'The type field is required.',
            'article_files.*.type.in' => 'The type must be one of the following: Article Text, Plagiarism Report, Research Instrument, Research Materials, Research Result, Transcripts, Data Analysis, Data Set, Source Texts, Other.'
        ];
    }
}
