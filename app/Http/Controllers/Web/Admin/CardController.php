<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{CardGroupRequest, CardRequest};
use App\Services\Admin\CardService;
use App\Models\{Card, CardGroup};
use App\Traits\HasApiResponse;
use Illuminate\Http\{Request, JsonResponse, RedirectResponse};
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{
    use HasApiResponse;

    public function __construct(
        protected CardService $cardService
    ) {}

    // ==================== CARD GROUP METHODS ====================

    /**
     * عرض قائمة مجموعات الكروت.
     */
    public function indexGroups(Request $request): View|JsonResponse
    {
        Gate::authorize('manage_cards');

        $groups = $this->cardService->listGroups(
            $request->only(['search']),
            $request->get('per_page', 15)
        );

        if ($request->wantsJson()) {
            return $this->paginatedResponse($groups, 'groups', 'تم استرجاع مجموعات الكروت بنجاح');
        }

        return view('spa.cards.groups.index', compact('groups'));
    }

    /**
     * إنشاء مجموعة كروت جديدة.
     */
    public function storeGroup(CardGroupRequest $request): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $group = $this->cardService->createGroup($request->validated());

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['group' => $group],
                    'تم إضافة مجموعة الكروت بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم إضافة مجموعة الكروت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating card group: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء إضافة مجموعة الكروت');
        }
    }

    /**
     * تحديث مجموعة كروت.
     */
    public function updateGroup(CardGroupRequest $request, CardGroup $group): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $updatedGroup = $this->cardService->updateGroup($group, $request->validated());

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['group' => $updatedGroup],
                    'تم تحديث مجموعة الكروت بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث مجموعة الكروت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating card group: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء تحديث مجموعة الكروت');
        }
    }

    /**
     * حذف مجموعة كروت.
     */
    public function destroyGroup(CardGroup $group): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $this->cardService->deleteGroup($group);

            if (request()->wantsJson()) {
                return $this->successResponse([], 'تم حذف مجموعة الكروت بنجاح');
            }

            return redirect()->back()->with('success', 'تم حذف مجموعة الكروت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting card group: ' . $e->getMessage());

            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف مجموعة الكروت');
        }
    }

    // ==================== CARD METHODS ====================

    /**
     * عرض قائمة الكروت.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('manage_cards');

        $cards = $this->cardService->listCards(
            $request->only(['search', 'group_id', 'type_id', 'status_id', 'holder_id']),
            $request->get('per_page', 15)
        );

        $groups = CardGroup::all();
        $types = \App\Models\LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))->get();
        $statuses = \App\Models\LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($cards, 'cards', 'تم استرجاع الكروت بنجاح');
        }

        return view('spa.cards.index', compact('cards', 'groups', 'types', 'statuses'));
    }

    public function indexByGroup(CardGroup $group, Request $request): View|JsonResponse
    {
        Gate::authorize('manage_cards');

        $cards = $this->cardService->listCardsByGroup(
            $group,
            $request->only(['search', 'status_id', 'type_id']),
            $request->get('per_page', 15)
        );

        $types = \App\Models\LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_TYPE'))->get();
        $statuses = \App\Models\LookupValue::whereHas('master', fn($q) => $q->where('code', 'CARD_STATUS'))->get();

        if ($request->wantsJson()) {
            return $this->paginatedResponse($cards, 'cards', 'تم استرجاع كروت المجموعة بنجاح');
        }

        return view('spa.cards.index', compact('group', 'cards', 'types', 'statuses'));
    }

    /**
     * إنشاء كرت جديد.
     */
    public function store(CardRequest $request, CardGroup $group): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $data = $request->validated();
            $data['card_group_id'] = $group->group_id; // Always use group from URL in nested route
            $card = $this->cardService->createCard($data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['card' => $card->loadCommonRelations()],
                    'تم إضافة الكرت بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم إضافة الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error creating card: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء إضافة الكرت');
        }
    }

    /**
     * تحديث بيانات الكرت.
     */
    public function update(CardRequest $request, CardGroup $group, Card $card): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $data = $request->validated();
            $data['card_group_id'] = $group->group_id; // Ensure consistent group context
            $updatedCard = $this->cardService->updateCard($card, $data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['card' => $updatedCard],
                    'تم تحديث الكرت بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating card: ' . $e->getMessage());

            return $this->handleError($e, $request, 'حدث خطأ أثناء تحديث الكرت');
        }
    }

    /**
     * حذف الكرت.
     */
    public function destroy(CardGroup $group, Card $card): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage_cards');

        try {
            $this->cardService->deleteCard($card);

            if (request()->wantsJson()) {
                return $this->successResponse([], 'تم حذف الكرت بنجاح');
            }

            return redirect()->back()->with('success', 'تم حذف الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting card: ' . $e->getMessage());

            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف الكرت');
        }
    }

    /**
     * تنشيط الكرت.
     */
    public function activate(Card $card): JsonResponse
    {
        Gate::authorize('manage_cards');

        try {
            $this->cardService->activateCard($card);

            return $this->successResponse([], 'تم تنشيط الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error activating card: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء تنشيط الكرت', 500);
        }
    }

    /**
     * تعطيل الكرت.
     */
    public function deactivate(Card $card): JsonResponse
    {
        Gate::authorize('manage_cards');

        try {
            $this->cardService->deactivateCard($card);

            return $this->successResponse([], 'تم تعطيل الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deactivating card: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء تعطيل الكرت', 500);
        }
    }

    /**
     * ربط الألبومات بالكرت.
     */
    public function linkAlbums(Request $request, Card $card): JsonResponse
    {
        Gate::authorize('manage_cards');

        $request->validate([
            'album_ids' => 'required|array',
            'album_ids.*' => 'exists:albums,album_id'
        ]);

        try {
            $this->cardService->linkCardToAlbums($card, $request->album_ids);

            return $this->successResponse([], 'تم ربط الألبومات بالكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error linking albums to card: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء ربط الألبومات بالكرت', 500);
        }
    }

    /**
     * الحصول على إحصائيات الكروت.
     */
    public function statistics(): JsonResponse
    {
        Gate::authorize('manage_cards');

        try {
            $stats = $this->cardService->getStatistics();

            return $this->successResponse(['statistics' => $stats], 'تم استرجاع الإحصائيات بنجاح');
        } catch (\Exception $e) {
            Log::error('Error fetching card statistics: ' . $e->getMessage());

            return $this->errorResponse('حدث خطأ أثناء استرجاع الإحصائيات', 500);
        }
    }

    // ==================== PRIVATE METHODS ====================

    private function handleError(\Exception $e, Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return $this->errorResponse(
                $message,
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }

        return back()->with('error', $message);
    }
}
