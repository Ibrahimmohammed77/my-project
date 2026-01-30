<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\StorageLibrary;
use App\UseCases\Studio\Storage\AllocateStorageUseCase;
use App\UseCases\Studio\Storage\UpdateStorageAllocationUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageLibraryController extends Controller
{
    protected $allocateStorageUseCase;
    protected $updateStorageAllocationUseCase;

    public function __construct(
        AllocateStorageUseCase $allocateStorageUseCase,
        UpdateStorageAllocationUseCase $updateStorageAllocationUseCase
    ) {
        $this->allocateStorageUseCase = $allocateStorageUseCase;
        $this->updateStorageAllocationUseCase = $updateStorageAllocationUseCase;
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

        return view('spa.studio-storage.index', compact('libraries'));
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
            'storage_limit' => 'required|numeric|min:0', // in Megabytes
        ]);

        // Convert MB to Bytes
        $validated['storage_limit'] = (int)($validated['storage_limit'] * 1024 * 1024);

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

    /**
     * Update an existing storage library.
     */
    public function update(Request $request, $id)
    {
        $studio = Auth::user()->studio;

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'storage_limit' => 'sometimes|required|numeric|min:0', // in Megabytes
        ]);

        if (isset($validated['storage_limit'])) {
            // Convert MB to Bytes
            $validated['storage_limit'] = (int)($validated['storage_limit'] * 1024 * 1024);
        }

        try {
            $library = $this->updateStorageAllocationUseCase->execute($studio, (int)$id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث بيانات مكتبة التخزين بنجاح',
                'data' => $library
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove a storage library.
     */
    public function destroy($id)
    {
        $studio = Auth::user()->studio;
        $library = $studio->storageLibraries()->findOrFail($id);

        if ($library->used_storage > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المكتبة لأنها تحتوي على ملفات'
            ], 422);
        }

        $library->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف مكتبة التخزين بنجاح'
        ]);
    }
}
