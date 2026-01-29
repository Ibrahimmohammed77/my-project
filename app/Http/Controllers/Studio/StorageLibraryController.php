<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\StorageLibrary;
use App\UseCases\Studio\Storage\AllocateStorageUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageLibraryController extends Controller
{
    protected $allocateStorageUseCase;

    public function __construct(AllocateStorageUseCase $allocateStorageUseCase)
    {
        $this->allocateStorageUseCase = $allocateStorageUseCase;
    }

    /**
     * List storage libraries for the studio.
     */
    public function index(Request $request)
    {
        $studio = Auth::user()->studio;
        $libraries = $studio->storageLibraries()->with('user')->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $libraries
            ]);
        }

        return view('studio.storage.index', compact('libraries'));
    }

    /**
     * Store a new storage library (allocate storage).
     */
    public function store(Request $request)
    {
        $studio = Auth::user()->studio;

        $validated = $request->validate([
            'subscriber_id' => 'required|exists:users,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'storage_limit' => 'required|integer|min:0', // in bytes, e.g. 1048576 for 1MB
        ]);

        try {
            $library = $this->allocateStorageUseCase->execute($studio, $validated);
            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء مكتبة التخزين وتخصيص المساحة بنجاح',
                'data' => $library
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
