<?php

namespace App\Http\Requests\api\v1\worktimeentries;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveWorktimeEntryRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'started_at' => [
                'date_format:Y-m-d\TH:i:sP',
            ],
            'ended_at' => [
                'date_format:Y-m-d\TH:i:sP',
            ],
            'project_id' => [
                'nullable',
                'exists:projects,id'
            ]
        ];

        if ($this->method() == 'POST') {
            $rules['started_at'][] = 'required';
        } else {
            $rules['started_at'][] = Rule::requiredIf(function () {
                return $this->ended_at == null;
            });
            $rules['ended_at'][] = Rule::requiredIf(function () {
                return $this->started_at == null;
            });
        }

        if ($this->ended_at) {
            $rules['started_at'][] = 'before_or_equal:ended_at';
        }

        if ($this->started_at) {
            $rules['ended_at'][] = 'after_or_equal:started_at';
        }

        return $rules;
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed|null  $keys
     * @return array
     */
    public function all($keys = null)
    {
        $data = parent::all($keys);

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $data['id'] = $this->route('worktime_entry');
        }

        return $data;
    }
}
