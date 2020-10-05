<?php

namespace App\Http\Requests\api\v1\worktimeentries;

use App\Models\WorktimeEntry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\worktimeentries\DateRule;
use Illuminate\Support\Facades\Auth;

class SaveWorktimeEntryRequest extends FormRequest
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
        $rules = [
            'started_at' => [
                'date_format:Y-m-d\TH:i:s',
                new DateRule($this->id)
            ],
            'ended_at' => [
                'date_format:Y-m-d\TH:i:s',
                new DateRule($this->id)
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator){
            if ($this->started_at && $this->ended_at) {
                $entryBetweenDates = WorktimeEntry::where([
                    ['user_id', Auth::user()->id],
                    ['started_at', '>=', $this->started_at],
                    ['ended_at', '<=', $this->ended_at]
                ]);

                if ($this->id) {
                    $entryBetweenDates->where('id', '<>', $this->id);
                }
                $entryBetweenDates = $entryBetweenDates->first();

                if (!empty($entryBetweenDates)) {
                    if (!$validator->errors()->first('started_at')) {
                        $validator->errors()->add('started_at', 'started_at must not collide with other worktime entries.');
                    }
                    if (!$validator->errors()->first('ended_at')) {
                        $validator->errors()->add('ended_at', 'ended_at must not collide with other worktime entries.');
                    }
                }
            }
        });
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
