<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LookupValueRequest;
use App\Models\LookupValue;
use App\UseCases\Admin\Lookup\ListLookupsUseCase;
use App\UseCases\Admin\Lookup\ManageLookupValueUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LookupController extends Controller
{
    protected $listLookupsUseCase;
    protected $manageLookupValueUseCase;

    public function __construct(
        ListLookupsUseCase $listLookupsUseCase,
        ManageLookupValueUseCase $manageLookupValueUseCase
    ) {
        $this->listLookupsUseCase = $listLookupsUseCase;
        $this->manageLookupValueUseCase = $manageLookupValueUseCase;
    }

    public function index()
    {
        Gate::authorize('manage_lookups');

        $masters = $this->listLookupsUseCase->execute();

        if (request()->wantsJson()) {
            return $this->successResponse(['masters' => $masters], 'تم استرجاع الثوابت بنجاح');
        }

        return view('spa.lookups.index', compact('masters'));
    }

    public function storeValue(LookupValueRequest $request)
    {
        Gate::authorize('manage_lookups');

        $this->manageLookupValueUseCase->create($request->validated());

        return redirect()->back()->with('success', 'تم إضافة العنصر بنجاح');
    }

    public function updateValue(LookupValueRequest $request, LookupValue $value)
    {
        Gate::authorize('manage_lookups');

        $this->manageLookupValueUseCase->update($value, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث العنصر بنجاح');
    }

    public function destroyValue(LookupValue $value)
    {
        Gate::authorize('manage_lookups');

        $this->manageLookupValueUseCase->delete($value);

        return redirect()->back()->with('success', 'تم حذف العنصر بنجاح');
    }
}
