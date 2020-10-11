<?php

namespace App\Http\Requests\api\v1\worktimeentries;

use App\Rules\worktimeentries\DateRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class GetWorktimeEntriesRequest extends FormRequest
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
                'nullable',
                'date',
            ],
            'ended_at' => [
                'nullable',
                'date',
            ],
        ];

        if ($this->started_at && $this->ended_at) {
            $rules['started_at'][] = 'before_or_equal:ended_at';
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

        $data['started_at'] = $this->query('started_at');
        $data['ended_at'] = $this->query('ended_at');

        return $data;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator){
            $this->started_at = $this->started_at
                ? Carbon::createFromFormat('Y-m-d', $this->started_at)->startOfDay()
                : ($this->ended_at
                    ? Carbon::createFromFormat('Y-m-d', $this->ended_at)->addDays(-14)
                    : Carbon::now()->addDays(-14));

            $this->ended_at = $this->ended_at
                ? Carbon::createFromFormat('Y-m-d', $this->ended_at)->endOfDay()
                : ($this->started_at
                    ? $this->started_at->copy()->addDays(14)
                    : Carbon::now());
        });
    }
}
