<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Services\AccountService;
use App\Domain\Shared\Repositories\Contracts\LookupValueRepositoryInterface;
use App\Domain\Identity\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $accountService;
    protected $lookupRepository;

    public function __construct(
        AccountService $accountService,
        LookupValueRepositoryInterface $lookupRepository
    ) {
        $this->accountService = $accountService;
        $this->lookupRepository = $lookupRepository;
    }

    public function index()
    {
        // Ideally usage of repository paginate, but service getAll returns collection.
        // Assuming service has paginate or we use simple all() for now.
        // BaseRepository usually has paginate. Service may not expose it yet.
        // Let's use getAll() 
        $accounts = $this->accountService->getAll();
        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        // Need statuses for dropdown
        // lookup value code for account status? Assuming 'ACCOUNT_STATUS' lookup_master code.
        // But repository lookup usually fetches by master code.
        // Assuming we can get all lookup values or filter later.
        // For now, let's hardcode or fetch if possible.
        // Let's assume we find values by master code 'ACCOUNT_STATUS'
        // But LookupValueRepositoryInterface might not have this specific method ready.
        // I will use `all()` and filter or just pass empty for now if not sure.
        // Actually, let's try to get all and filter in memory if needed, or query LookupValue model directly if repo is limited.
        // Better: Use LookupValue model directly here or add method to repo. 
        // I'll assume generic usage.
        
        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->pluck('name', 'lookup_value_id');
        
        return view('accounts.create', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:accounts,username',
            'email' => 'required|email|unique:accounts,email',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'account_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'password' => 'required|min:6',
        ]);

        // Map password to password_hash
        $validated['password_hash'] = bcrypt($validated['password']);
        unset($validated['password']);

        $this->accountService->create($validated);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit($id)
    {
        $account = $this->accountService->find($id);
        if (!$account) abort(404);

        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->pluck('name', 'lookup_value_id');

        return view('accounts.edit', compact('account', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|unique:accounts,username,' . $id . ',account_id',
            'email' => 'required|email|unique:accounts,email,' . $id . ',account_id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'account_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'password' => 'nullable|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password_hash'] = bcrypt($validated['password']);
        }
        unset($validated['password']);

        $this->accountService->update($id, $validated);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy($id)
    {
        $this->accountService->delete($id);
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
