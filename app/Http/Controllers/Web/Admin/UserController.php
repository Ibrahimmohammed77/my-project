<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\UseCases\Admin\CreateUserUseCase;
use App\UseCases\Admin\ListUsersUseCase;
use App\Models\Role;
use App\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $createUserUseCase;
    protected $listUsersUseCase;

    public function __construct(
        CreateUserUseCase $createUserUseCase,
        ListUsersUseCase $listUsersUseCase
    ) {
        $this->createUserUseCase = $createUserUseCase;
        $this->listUsersUseCase = $listUsersUseCase;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'role_id', 'status_id', 'type_id']);
        $users = $this->listUsersUseCase->execute($filters, $request->get('per_page', 15));

        $roles = Role::all();
        $statuses = LookupValue::whereHas('master', function ($q) {
            $q->where('code', 'user_status');
        })->get();

        return view('spa.accounts.index', compact('users', 'roles', 'statuses', 'filters'));
    }

    /**
     * Store a newly created user.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            $this->createUserUseCase->execute($request->validated());

            return redirect()->route('spa.accounts')
                ->with('success', 'تم إنشاء الحساب بنجاح!');
        } catch (\Exception $e) {
            Log::error('Admin user creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'فشل في إنشاء الحساب: ' . $e->getMessage());
        }
    }
}
