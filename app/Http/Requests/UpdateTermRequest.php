<?php

namespace App\Http\Requests;

use App\Models\Term;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTermRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('term_edit');
    }

    public function rules()
    {
        return [];
    }
}
