<?php

namespace App\Http\Controllers;

use App\Domain\Core\Services\CustomerService;
use App\Domain\Core\Requests\StoreCustomerRequest;
use App\Domain\Core\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $customers = $this->customerService->getAll();
            $customers->load(['account', 'gender']);
            
            return response()->json([
                'success' => true,
                'data' => ['customers' => $customers]
            ]);
        }

        return view('spa.customers.index');
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->customerService->create($request->validated());
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $customer = $this->customerService->find($id);
        if (!$customer) return response()->json(['message' => 'Not found'], 404);
        
        $customer->load(['account', 'gender']);
        return response()->json(['success' => true, 'data' => ['customer' => $customer]]);
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $this->customerService->update($id, $request->validated());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $this->customerService->delete($id);
        return response()->json(['success' => true]);
    }
}
