<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;

class UpdatePlanRequest extends BasePlanRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage_plans');
    }
}
