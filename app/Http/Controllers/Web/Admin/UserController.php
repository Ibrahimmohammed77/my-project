<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\UseCases\Admin\CreateUserUseCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $createUserUseCase;

    public function __construct(CreateUserUseCase $createUserUseCase)
    {
        $this->createUserUseCase = $createUserUseCase;
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
