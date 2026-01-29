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

        return view('studio.cards.index', compact('cards'));
    }

    public function show(Card $card)
    {
        $this->authorize('view', $card);

        $studio = Auth::user()->studio;
        $card->load(['type', 'status', 'albums']);
        
        $availableAlbums = $studio->albums()->orderBy('name')->get();

        return view('studio.cards.show', compact('card', 'availableAlbums'));
    }

    public function linkAlbums(LinkAlbumsRequest $request, Card $card)
    {
        $studio = Auth::user()->studio;
        $validated = $request->validated();

        try {
            $this->linkCardToAlbumUseCase->execute($studio, $card->card_id, $validated['album_ids']);

            return redirect()->route('studio.cards.show', $card->card_id)->with('success', 'تم ربط الكرت بالألبومات بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

}
