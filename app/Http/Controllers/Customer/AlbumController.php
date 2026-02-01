<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\{Album, Customer};
use App\UseCases\Albums\{CreateAlbumUseCase, UpdateAlbumUseCase};
use App\UseCases\Photos\UploadPhotoUseCase;
use App\Traits\HasApiResponse;
use Illuminate\Http\{Request, JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\{Auth, Log};
use Illuminate\View\View;

class AlbumController extends Controller
{
    use HasApiResponse;

    public function __construct(
        protected CreateAlbumUseCase $createAlbumUseCase,
        protected UpdateAlbumUseCase $updateAlbumUseCase,
        protected UploadPhotoUseCase $uploadPhotoUseCase
    ) {}

    /**
     * Display a listing of customer's albums.
     */
    public function index(Request $request): View|JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            abort(403, 'غير مصرح لك بالوصول');
        }

        $albums = $customer->albums()
            ->with(['storageLibrary'])
            ->withCount('photos')
            ->when($request->search, function($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        if ($request->wantsJson()) {
            return $this->paginatedResponse($albums, 'albums');
        }

        return view('spa.customer-albums.index', [
            'albums' => $albums,
            'customer' => $customer
        ]);
    }

    /**
     * Store a newly created album.
     */
    public function store(Request $request): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            return $this->errorResponse('غير مصرح لك بالوصول', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'storage_library_id' => 'required|exists:storage_libraries,storage_library_id',
            'is_visible' => 'boolean',
        ]);

        try {
            // Verify storage library belongs to customer
            $storageLibrary = $customer->user->storageLibraries()
                ->where('storage_library_id', $validated['storage_library_id'])
                ->firstOrFail();

            $album = $this->createAlbumUseCase->execute(
                $customer,
                $validated['name'],
                $validated['description'] ?? null,
                $storageLibrary->storage_library_id,
                $validated['is_visible'] ?? true
            );

            return $this->successResponse(
                ['album' => $album->load('storageLibrary')],
                'تم إنشاء الألبوم بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error creating customer album: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء الألبوم', 500);
        }
    }

    /**
     * Update the specified album.
     */
    public function update(Request $request, Album $album): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $album->owner_id !== $customer->customer_id || $album->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك بتعديل هذا الألبوم', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_visible' => 'boolean',
        ]);

        try {
            $album = $this->updateAlbumUseCase->execute($album, $validated);

            return $this->successResponse(
                ['album' => $album->fresh()->load('storageLibrary')],
                'تم تحديث الألبوم بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error updating customer album: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الألبوم', 500);
        }
    }

    /**
     * Remove the specified album.
     */
    public function destroy(Album $album): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $album->owner_id !== $customer->customer_id || $album->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك بحذف هذا الألبوم', 403);
        }

        try {
            $album->delete();

            return $this->successResponse([], 'تم حذف الألبوم بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting customer album: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الألبوم', 500);
        }
    }

    /**
     * Upload photos to an album.
     */
    public function uploadPhotos(Request $request, Album $album): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $album->owner_id !== $customer->customer_id || $album->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك برفع صور لهذا الألبوم', 403);
        }

        $validated = $request->validate([
            'photos' => 'required|array|min:1',
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'caption' => 'nullable|string|max:255',
        ]);

        try {
            $uploadedPhotos = [];

            foreach ($validated['photos'] as $photo) {
                $uploadedPhoto = $this->uploadPhotoUseCase->execute($album, $photo, [
                    'caption' => $validated['caption'] ?? null,
                ]);
                $uploadedPhotos[] = $uploadedPhoto;
            }

            return $this->successResponse(
                ['photos' => $uploadedPhotos],
                'تم رفع الصور بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error uploading photos to customer album: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء رفع الصور', 500);
        }
    }
}
