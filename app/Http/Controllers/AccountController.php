<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Services\AccountService;
use App\Domain\Shared\Repositories\Contracts\LookupValueRepositoryInterface;
use App\Domain\Identity\Models\Account;
use App\Domain\Identity\Requests\StoreAccountRequest;
use App\Domain\Identity\Requests\UpdateAccountRequest;
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

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $accounts = $this->accountService->getAll();
            $accounts->load(['status', 'roles']);

            return response()->json([
                'success' => true,
                'data' => ['accounts' => $accounts]
            ]);
        }
        
        // Fetch Account Types
        $types = \App\Domain\Shared\Models\LookupValue::category('ACCOUNT_TYPE')->get(['lookup_value_id', 'code', 'name']);
        
        // Map types to IDs for JS (e.g., { 'STUDIO': 1, 'SCHOOL': 2 ... })
        $accountTypeIds = $types->pluck('lookup_value_id', 'code');

        // Fetch Studio Lookups
        $studioStatuses = \App\Domain\Shared\Models\LookupValue::category('STUDIO_STATUS')->get(['lookup_value_id', 'name']);

        // Fetch School Lookups
        $schoolTypes = \App\Domain\Shared\Models\LookupValue::category('SCHOOL_TYPE')->get(['lookup_value_id', 'name']);
        $schoolLevels = \App\Domain\Shared\Models\LookupValue::category('SCHOOL_LEVEL')->get(['lookup_value_id', 'name']);
        $schoolStatuses = \App\Domain\Shared\Models\LookupValue::category('SCHOOL_STATUS')->get(['lookup_value_id', 'name']);

        // Fetch Subscriber Lookups
        $subscriberStatuses = \App\Domain\Shared\Models\LookupValue::category('SUBSCRIBER_STATUS')->get(['lookup_value_id', 'name']);

        return view('spa.accounts.index', compact(
            'types', 
            'accountTypeIds', 
            'studioStatuses', 
            'schoolTypes', 
            'schoolLevels', 
            'schoolStatuses', 
            'subscriberStatuses'
        ));
    }

    public function create()
    {
        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->pluck('name', 'lookup_value_id');
        
        return view('accounts.create', compact('statuses'));
    }

    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password_hash'] = bcrypt($data['password']);
            unset($data['password']);
        }

        // Create Account
        $account = $this->accountService->create($data);

        // check account type and create related entity
        $accountType = \App\Domain\Shared\Models\LookupValue::find($data['account_type_id']);
        
        if ($accountType) {
            switch ($accountType->code) {
                case 'STUDIO':
                    app(\App\Domain\Core\Services\StudioService::class)->create([
                        'account_id' => $account->account_id,
                    ]);
                    break;
                case 'SCHOOL':
                    app(\App\Domain\Core\Services\SchoolService::class)->create([
                        'account_id' => $account->account_id,
                        'school_type_id' => $data['school_type_id'] ?? null,
                        'school_level_id' => $data['school_level_id'] ?? null,
                    ]);
                    break;
                case 'SUBSCRIBER':
                    app(\App\Domain\Core\Services\SubscriberService::class)->create([
                        'account_id' => $account->account_id,
                    ]);
                    break;
            }
        }

        return response()->json(['success' => true]);
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

    public function update(UpdateAccountRequest $request, $id)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password_hash'] = bcrypt($data['password']);
        }
        unset($data['password']);

        // Remove cached requests parameters if any (like _method, etc) which validated() filters anyway.
        
        $this->accountService->update($id, $data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy($id)
    {
        $this->accountService->delete($id);
        
        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
