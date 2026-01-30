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
}
