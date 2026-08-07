<?php

namespace App\Http\Requests\Concerns;

use App\Models\Tender;
use App\Rules\NotEmptyHtml;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait ValidatesTenderFields
{
    /**
     * Build validation rules based on the selected tender category.
     *
     * @return array<string, mixed>
     */
    protected function tenderRules(Request $request, bool $isUpdate = false): array
    {
        $category = $request->input('category', Tender::CATEGORY_SELECT);

        $rules = [
            'category' => [
                'required',
                'string',
                Rule::notIn([Tender::CATEGORY_SELECT]),
                Rule::in(array_keys(Tender::categories())),
            ],
            'uploaded_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:uploaded_date'],
            'tender_file' => [
                $isUpdate ? 'nullable' : 'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
        ];

        if (Tender::usesTitleFields($category)) {
            $rules['title'] = ['required', 'string'];
            $rules['tender_no'] = ['nullable'];
            $rules['description'] = ['nullable'];
        } elseif (Tender::usesDetailFields($category)) {
            $rules['title'] = ['nullable'];
            $rules['tender_no'] = ['required', 'string', 'max:100'];
            $rules['description'] = ['required', 'string', new NotEmptyHtml];
        }

        return $rules;
    }

    /**
     * Normalize validated data and clear hidden fields.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeTenderData(array $data): array
    {
        if (Tender::usesTitleFields($data['category'])) {
            $data['tender_no'] = null;
            $data['description'] = null;
        } elseif (Tender::usesDetailFields($data['category'])) {
            $data['title'] = null;
            $data['description'] = clean($data['description'], 'news');
        }

        return $data;
    }
}
