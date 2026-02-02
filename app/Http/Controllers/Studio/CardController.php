<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Studio\LinkAlbumsRequest;

class CardController extends Controller
{
    protected $linkCardToAlbumUseCase;

    public function __construct(\App\UseCases\Studio\Card\LinkCardToAlbumUseCase $linkCardToAlbumUseCase)
    {
        $this->linkCardToAlbumUseCase = $linkCardToAlbumUseCase;
    }

    public function index()
    {
        $this->authorize('viewAny', Card::class);
        
        $studio = Auth::user()->studio;
        $cards = $studio->cards()->with('type', 'status')->latest()->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cards' => $cards
                ]
            ]);
        }

        return view('spa.studio-cards.index', compact('cards'));
    }

    public function show(Card $card)
    {
        $this->authorize('view', $card);

        $studio = Auth::user()->studio;
        $card->load(['type', 'status', 'albums']);
        
        $availableAlbums = $studio->albums()->orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'card' => $card,
                    'availableAlbums' => $availableAlbums
                ]
            ]);
        }

        return view('spa.studio-cards.show', compact('card', 'availableAlbums'));
    }

    public function linkAlbums(LinkAlbumsRequest $request, Card $card)
    {
        $studio = Auth::user()->studio;
        $validated = $request->validated();

        try {
            $this->linkCardToAlbumUseCase->execute($studio, $card->card_id, $validated['album_ids']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم ربط الكرت بالألبومات بنجاح'
                ]);
            }

            return redirect()->route('studio.cards.show', $card->card_id)->with('success', 'تم ربط الكرت بالألبومات بنجاح');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * ربط الكرت بمكتبة التخزين (مساحة)
     */
    public function assignToLibrary(Request $request, Card $card)
    {
        $this->authorize('update', $card);
        
        $validated = $request->validate([
            'storage_library_id' => 'required|exists:storage_libraries,storage_library_id',
        ]);

        try {
            $studio = Auth::user()->studio;
            
            // التحقق من أن المكتبة تابعة للاستوديو
            $library = $studio->storageLibraries()->findOrFail($validated['storage_library_id']);
            
            $useCase = app(\App\UseCases\Studio\AssignCardToLibraryUseCase::class);
            $card = $useCase->execute($card, $library);

            return response()->json([
                'success' => true,
                'message' => 'تم ربط الكرت بمكتبة التخزين بنجاح',
                'data' => $card
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
