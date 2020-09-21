<?php

namespace App\Http\Requests\api\v1\worktimeentries;

use App\Rules\worktimeentries\DateRule;
use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'started_at' => [
                'nullable',
                'date',
                'before_or_equal:ended_at',
            ],
            'ended_at' => [
                'nullable',
                'date',
                'after_or_equal:started_at',
            ],
        ];
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
}
