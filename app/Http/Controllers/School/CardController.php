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

    public function index(Request $request)
    {
        $school = Auth::user()->school;
        $query = $school->cards()->with('type', 'status')->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('card_number', 'like', '%' . $search . '%');
        }

        if ($request->has('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        $cards = $query->paginate($request->get('per_page', 10));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cards' => $cards->items(),
                    'pagination' => [
                        'total' => $cards->total(),
                        'per_page' => $cards->perPage(),
                        'current_page' => $cards->currentPage(),
                        'last_page' => $cards->lastPage(),
                        'from' => $cards->firstItem(),
                        'to' => $cards->lastItem()
                    ]
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على الكارت'
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
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
