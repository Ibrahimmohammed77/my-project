<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\{Card, Customer, Album};
use App\Traits\HasApiResponse;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Auth, Log, DB};
use Illuminate\View\View;

class CardController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of customer's cards.
     */
    public function index(Request $request): View|JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            abort(403, 'غير مصرح لك بالوصول');
        }

        $cards = $customer->cards()
            ->with(['albums'])
            ->withCount('albums')
            ->when($request->search, function($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        if ($request->wantsJson()) {
            return $this->paginatedResponse($cards, 'cards');
        }

        return view('spa.customer-cards.index', [
            'cards' => $cards,
            'customer' => $customer
        ]);
    }

    /**
     * Store a newly created card.
     */
    public function store(Request $request): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            return $this->errorResponse('غير مصرح لك بالوصول', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'qr_code' => 'nullable|string|max:255|unique:cards,qr_code',
            'is_active' => 'boolean',
        ]);

        try {
            $card = Card::create([
                'owner_type' => Customer::class,
                'owner_id' => $customer->customer_id,
                'title' => $validated['title'],
                'qr_code' => $validated['qr_code'] ?? $this->generateQRCode(),
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return $this->successResponse(
                ['card' => $card],
                'تم إنشاء الكرت بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error creating customer card: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء الكرت', 500);
        }
    }

    /**
     * Update the specified card.
     */
    public function update(Request $request, Card $card): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $card->owner_id !== $customer->customer_id || $card->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك بتعديل هذا الكرت', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $card->update($validated);

            return $this->successResponse(
                ['card' => $card->fresh()],
                'تم تحديث الكرت بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error updating customer card: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث الكرت', 500);
        }
    }

    /**
     * Remove the specified card.
     */
    public function destroy(Card $card): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $card->owner_id !== $customer->customer_id || $card->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك بحذف هذا الكرت', 403);
        }

        try {
            $card->delete();

            return $this->successResponse([], 'تم حذف الكرت بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting customer card: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف الكرت', 500);
        }
    }

    /**
     * Link albums to a card.
     */
    public function linkAlbums(Request $request, Card $card): JsonResponse
    {
        $customer = Auth::user()->customer;

        if (!$customer || $card->owner_id !== $customer->customer_id || $card->owner_type !== Customer::class) {
            return $this->errorResponse('غير مصرح لك بربط ألبومات لهذا الكرت', 403);
        }

        $validated = $request->validate([
            'album_ids' => 'required|array',
            'album_ids.*' => 'required|exists:albums,album_id',
        ]);

        try {
            // Verify all albums belong to customer
            $albums = Album::whereIn('album_id', $validated['album_ids'])
                ->where('owner_type', Customer::class)
                ->where('owner_id', $customer->customer_id)
                ->pluck('album_id');

            if ($albums->count() !== count($validated['album_ids'])) {
                return $this->errorResponse('بعض الألبومات غير موجودة أو لا تخصك', 403);
            }

            // Sync albums
            $card->albums()->sync($validated['album_ids']);

            return $this->successResponse(
                ['card' => $card->load('albums')],
                'تم ربط الألبومات بالكرت بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error linking albums to customer card: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء ربط الألبومات', 500);
        }
    }

    /**
     * Generate a unique QR code.
     */
    private function generateQRCode(): string
    {
        do {
            $qrCode = 'CUST-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
        } while (Card::where('qr_code', $qrCode)->exists());

        return $qrCode;
    }
}
