<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CardGroupRequest;
use App\Http\Requests\Admin\CardRequest;
use App\Models\Card;
use App\Models\CardGroup;
use App\UseCases\Admin\Card\ManageCardGroupUseCase;
use App\UseCases\Admin\Card\ManageCardUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CardController extends Controller
{
    protected $manageCardGroupUseCase;
    protected $manageCardUseCase;

    public function __construct(
        ManageCardGroupUseCase $manageCardGroupUseCase,
        ManageCardUseCase $manageCardUseCase
    ) {
        $this->manageCardGroupUseCase = $manageCardGroupUseCase;
        $this->manageCardUseCase = $manageCardUseCase;
    }

    // --- Card Group Methods ---

    public function indexGroup()
    {
        Gate::authorize('manage_cards');

        $groups = $this->manageCardGroupUseCase->listGroups();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'groups' => $groups
                ]
            ]);
        }

        return view('spa.cards.groups.index', compact('groups'));
    }

    public function storeGroup(CardGroupRequest $request)
    {
        Gate::authorize('manage_cards');

        $this->manageCardGroupUseCase->createGroup($request->validated());

        return redirect()->back()->with('success', 'تم إضافة مجموعة الكروت بنجاح');
    }

    public function updateGroup(CardGroupRequest $request, CardGroup $group)
    {
        Gate::authorize('manage_cards');

        $this->manageCardGroupUseCase->updateGroup($group, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث مجموعة الكروت بنجاح');
    }

    public function destroyGroup(CardGroup $group)
    {
        Gate::authorize('manage_cards');

        $this->manageCardGroupUseCase->deleteGroup($group);

        return redirect()->back()->with('success', 'تم حذف مجموعة الكروت بنجاح');
    }

    // --- Card Methods (Nested within Group) ---

    public function indexCards(CardGroup $group, Request $request)
    {
        Gate::authorize('manage_cards');

        $cards = $this->manageCardUseCase->listCardsByGroup(
            $group,
            $request->only(['search', 'status_id']),
            $request->get('per_page', 15)
        );

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'cards' => $cards
                ]
            ]);
        }

        return view('spa.cards.index', compact('group', 'cards'));
    }

    public function storeCard(CardRequest $request, CardGroup $group)
    {
        Gate::authorize('manage_cards');
        
        // Force group ID from route to ensure consistency
        $data = $request->validated();
        $data['card_group_id'] = $group->group_id;

        $this->manageCardUseCase->createCard($data);

        return redirect()->back()->with('success', 'تم إضافة الكرت بنجاح');
    }

    public function updateCard(CardRequest $request, CardGroup $group, Card $card)
    {
        Gate::authorize('manage_cards');

        // Verify card belongs to group
        if ($card->card_group_id !== $group->group_id) {
            abort(404);
        }

        $this->manageCardUseCase->updateCard($card, $request->validated());

        return redirect()->back()->with('success', 'تم تحديث الكرت بنجاح');
    }

    public function destroyCard(CardGroup $group, Card $card)
    {
        Gate::authorize('manage_cards');

        if ($card->card_group_id !== $group->group_id) {
            abort(404);
        }

        $this->manageCardUseCase->deleteCard($card);

        return redirect()->back()->with('success', 'تم حذف الكرت بنجاح');
    }
}
