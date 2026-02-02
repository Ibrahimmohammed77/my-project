<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\UseCases\RedeemCardUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CardRedemptionController extends Controller
{
    /**
     * استخدام/استرداد الكرت من قبل المستخدم
     */
    public function redeem(Request $request, RedeemCardUseCase $useCase): JsonResponse
    {
        $validated = $request->validate([
            'card_number' => 'required|string',
        ]);

        try {
            $user = $request->user();
            $card = $useCase->execute($user, $validated['card_number']);

            return response()->json([
                'success' => true,
                'message' => 'تم استخدام الكرت بنجاح! يمكنك الآن الوصول إلى الألبوم',
                'data' => [
                    'card' => $card,
                    'storage_library' => $card->storageLibrary,
                    'hidden_album' => $card->storageLibrary->hiddenAlbum ?? null,
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Card not found', [
                'user_id' => $request->user()->id,
                'card_number' => $validated['card_number'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'الكرت غير موجود',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Card redemption failed', [
                'user_id' => $request->user()->id,
                'card_number' => $validated['card_number'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * عرض الكروت التي استخدمها المستخدم
     */
    public function myCards(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            $cards = \App\Models\Card::where('holder_id', $user->id)
                ->with(['storageLibrary.hiddenAlbum', 'type', 'status'])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cards,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user cards', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'فشل في جلب الكروت',
            ], 500);
        }
    }
}
