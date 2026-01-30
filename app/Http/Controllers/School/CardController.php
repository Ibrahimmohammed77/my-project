<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Http\Requests\School\LinkSchoolAlbumsRequest;
use App\UseCases\School\Card\LinkSchoolCardToAlbumUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    protected $linkCardToAlbumUseCase;

    public function __construct(LinkSchoolCardToAlbumUseCase $linkCardToAlbumUseCase)
    {
        $this->linkCardToAlbumUseCase = $linkCardToAlbumUseCase;
    }

    public function index()
    {
        $school = Auth::user()->school;
        $cards = $school->cards()->with('type', 'status')->latest()->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cards' => $cards
                ]
            ]);
        }

        return view('spa.school-cards.index', compact('cards'));
    }

    public function show($id)
    {
        $school = Auth::user()->school;
        $card = $school->cards()->with(['type', 'status', 'albums'])->findOrFail($id);
        
        $availableAlbums = $school->albums()->orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'card' => $card,
                    'availableAlbums' => $availableAlbums
                ]
            ]);
        }

        return view('spa.school-cards.show', compact('card', 'availableAlbums'));
    }

    public function linkAlbums(LinkSchoolAlbumsRequest $request, $id)
    {
        $school = Auth::user()->school;
        $validated = $request->validated();

        try {
            $this->linkCardToAlbumUseCase->execute($school, (int)$id, $validated['album_ids']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم ربط كارت المدرسة بالألبومات بنجاح'
                ]);
            }

            return redirect()->route('school.cards.show', $id)->with('success', 'تم ربط كارت المدرسة بالألبومات بنجاح');
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
}
