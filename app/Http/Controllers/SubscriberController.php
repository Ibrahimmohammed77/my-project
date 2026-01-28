<?php

namespace App\Http\Controllers;

use App\Domain\Core\Services\SubscriberService;
use App\Domain\Core\Requests\StoreSubscriberRequest;
use App\Domain\Core\Requests\UpdateSubscriberRequest;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    protected $subscriberService;

    public function __construct(SubscriberService $subscriberService)
    {
        $this->subscriberService = $subscriberService;
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $subscribers = $this->subscriberService->getAll();
            $subscribers->load(['account', 'status']);
            
            return response()->json([
                'success' => true,
                'data' => ['subscribers' => $subscribers]
            ]);
        }
        
        // Load statuses for the view
        $statuses = \App\Domain\Shared\Models\LookupValue::whereHas('master', function($q) {
            $q->where('code', 'SUBSCRIBER_STATUS');
        })->get(['lookup_value_id', 'name']);

        return view('spa.subscribers.index', compact('statuses'));
    }

    public function store(StoreSubscriberRequest $request)
    {
        $this->subscriberService->create($request->validated());
        return response()->json(['success' => true]);
    }

    public function show($id)
    {
        $subscriber = $this->subscriberService->find($id);
        if (!$subscriber) return response()->json(['message' => 'Not found'], 404);
        
        $subscriber->load(['account', 'status']);
        return response()->json(['success' => true, 'data' => ['subscriber' => $subscriber]]);
    }

    public function update(UpdateSubscriberRequest $request, $id)
    {
        $this->subscriberService->update($id, $request->validated());
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $this->subscriberService->delete($id);
        return response()->json(['success' => true]);
    }
}
