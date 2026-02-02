<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolAlbumRequest;
use App\Http\Requests\School\UpdateSchoolAlbumRequest;
use App\Models\Album;
use App\Models\StorageLibrary;
use App\UseCases\School\Album\CreateSchoolAlbumUseCase;
use App\UseCases\School\Album\LinkSchoolAlbumToCardUseCase;
use App\UseCases\School\Album\UpdateSchoolAlbumUseCase;
use App\Helpers\StorageLibraryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    protected $createAlbumUseCase;
    protected $updateAlbumUseCase;
    protected $linkAlbumToCardUseCase;

    public function __construct(
        CreateSchoolAlbumUseCase $createAlbumUseCase,
        UpdateSchoolAlbumUseCase $updateSchoolAlbumUseCase,
        LinkSchoolAlbumToCardUseCase $linkAlbumToCardUseCase
    ) {
        $this->createAlbumUseCase = $createAlbumUseCase;
        $this->updateAlbumUseCase = $updateSchoolAlbumUseCase;
        $this->linkAlbumToCardUseCase = $linkAlbumToCardUseCase;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $school = Auth::user()->school;
        
        // Ensure storage library exists (fallback)
        try {
            StorageLibraryHelper::ensureStorageLibraryForSchool($school);
        } catch (\Exception $e) {
            // If this fails, user will get proper error when trying to create album
            \Log::warning('Could not ensure storage library for school ' . $school->school_id . ': ' . $e->getMessage());
        }
        
        $query = $school->albums()->withCount('photos')->latest();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $albums = $query->paginate($request->get('per_page', 10));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'albums' => $albums->items(),
                    'pagination' => [
                        'total' => $albums->total(),
                        'per_page' => $albums->perPage(),
                        'current_page' => $albums->currentPage(),
                        'last_page' => $albums->lastPage(),
                        'from' => $albums->firstItem(),
                        'to' => $albums->lastItem()
                    ]
                ]
            ]);
        }

        return view('spa.school-albums.index', compact('albums'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolAlbumRequest $request)
    {
        $school = Auth::user()->school;
        $validated = $request->validated();

        try {
            $album = $this->createAlbumUseCase->execute($school, $validated);

            if (!empty($validated['card_ids'])) {
                $this->linkAlbumToCardUseCase->execute($school, $album->album_id, $validated['card_ids']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء ألبوم المدرسة بنجاح',
                    'data' => $album
                ]);
            }

            return redirect()->route('school.albums.index')->with('success', 'تم إنشاء ألبوم المدرسة بنجاح');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolAlbumRequest $request, $id)
    {
        $school = Auth::user()->school;
        $validated = $request->validated();

        try {
            // Ensure the album belongs to the school
            $albumModel = $school->albums()->findOrFail($id);

            $album = $this->updateAlbumUseCase->execute($school, (int)$id, $validated);

            if (isset($validated['card_ids'])) {
                $this->linkAlbumToCardUseCase->execute($school, $album->album_id, $validated['card_ids']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث ألبوم المدرسة بنجاح',
                    'data' => $album
                ]);
            }

            return redirect()->route('school.albums.index')->with('success', 'تم تحديث ألبوم المدرسة بنجاح');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الألبوم'
                ], 404);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $school = Auth::user()->school;
        
        try {
            $album = $school->albums()->findOrFail($id);
            $album->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف ألبوم المدرسة بنجاح'
                ]);
            }

            return redirect()->route('school.albums.index')->with('success', 'تم حذف ألبوم المدرسة بنجاح');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الألبوم'
                ], 404);
            }
            throw $e;
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    /**
     * Upload photos to the album.
     */
    public function uploadPhotos(Request $request, $id, \App\UseCases\Photos\UploadPhotoUseCase $uploadPhotoUseCase)
    {
        $school = Auth::user()->school;
        $album = $school->albums()->findOrFail($id);

        $request->validate([
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'caption' => 'nullable|string|max:255',
        ]);

        $uploadedPhotos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $uploadedPhotos[] = $uploadPhotoUseCase->execute($album, $file, [
                    'caption' => $request->caption,
                ]);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم رفع الصور بنجاح',
                'data' => $uploadedPhotos
            ]);
        }

        return redirect()->back()->with('success', 'تم رفع الصور بنجاح');
    }
}
