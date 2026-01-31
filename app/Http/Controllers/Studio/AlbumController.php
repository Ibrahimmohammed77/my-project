<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Http\Requests\Studio\StoreAlbumRequest;
use App\Http\Requests\Studio\UpdateAlbumRequest;
use App\Models\Album;
use App\Models\StorageLibrary;
use App\UseCases\Studio\Album\CreateAlbumUseCase;
use App\UseCases\Studio\Album\LinkAlbumToCardUseCase;
use App\UseCases\Studio\Album\UpdateAlbumUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    protected $createAlbumUseCase;
    protected $updateAlbumUseCase;
    protected $linkAlbumToCardUseCase;

    public function __construct(
        CreateAlbumUseCase $createAlbumUseCase,
        UpdateAlbumUseCase $updateAlbumUseCase,
        LinkAlbumToCardUseCase $linkAlbumToCardUseCase
    ) {
        $this->createAlbumUseCase = $createAlbumUseCase;
        $this->updateAlbumUseCase = $updateAlbumUseCase;
        $this->linkAlbumToCardUseCase = $linkAlbumToCardUseCase;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Album::class);
        
        $studio = Auth::user()->studio;
        $albums = $studio->albums()->withCount('photos')->latest()->get();

        $libraries = $studio->storageLibraries;

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'albums' => $albums,
                    'libraries' => $libraries
                ]
            ]);
        }

        return view('spa.studio-albums.index', compact('albums', 'libraries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Album::class);
        
        $studio = Auth::user()->studio;
        $libraries = $studio->storageLibraries;
        return view('studio.albums.create', compact('libraries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlbumRequest $request)
    {
        $studio = Auth::user()->studio;
        $validated = $request->validated();

        try {
            $album = $this->createAlbumUseCase->execute($studio, $validated);

            if (!empty($validated['card_ids'])) {
                $this->linkAlbumToCardUseCase->execute($studio, $album->album_id, $validated['card_ids']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء الألبوم بنجاح',
                    'data' => $album
                ]);
            }

            return redirect()->route('studio.albums.index')->with('success', 'تم إنشاء الألبوم بنجاح');
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
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $studio = Auth::user()->studio;
        $album = $studio->albums()->with('cards')->findOrFail($id);
        
        $this->authorize('update', $album);

        $libraries = $studio->storageLibraries;
        $cards = $studio->cards; 

        return view('studio.albums.edit', compact('album', 'libraries', 'cards'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlbumRequest $request, $id)
    {
        $studio = Auth::user()->studio;
        $album = $studio->albums()->findOrFail($id);
        
        $this->authorize('update', $album);
        
        $validated = $request->validated();

        try {
            $album = $this->updateAlbumUseCase->execute($studio, $id, $validated);

            if (isset($validated['card_ids'])) {
                $this->linkAlbumToCardUseCase->execute($studio, $album->album_id, $validated['card_ids']);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث الألبوم بنجاح',
                    'data' => $album
                ]);
            }

            return redirect()->route('studio.albums.index')->with('success', 'تم تحديث الألبوم بنجاح');
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
        $studio = Auth::user()->studio;
        $album = $studio->albums()->findOrFail($id);
        
        $this->authorize('delete', $album);

        $album->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حذف الألبوم بنجاح'
            ]);
        }

        return redirect()->route('studio.albums.index')->with('success', 'تم حذف الألبوم بنجاح');
    }
}
