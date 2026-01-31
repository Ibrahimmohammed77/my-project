<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

use App\Traits\HasApiResponse;

class PermissionController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): View|JsonResponse
    {
        $permissions = Permission::active()->get();

        if ($request->wantsJson()) {
            return $this->successResponse(['permissions' => $permissions]);
        }

        return view('spa.permissions.index', compact('permissions'));
    }
}
