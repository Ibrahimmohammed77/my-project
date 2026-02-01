<?php

namespace App\UseCases;

use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RedeemCardUseCase
{
    /**
     * استخدام الكرت: ربط المستخدم بالكرت ومنحه الوصول للألبوم المخفي
     *
     * @param User $user
     * @param string $cardNumber
     * @return Card
     * @throws \Exception
     */
    public function execute(User $user, string $cardNumber): Card
    {
        DB::beginTransaction();
        
        try {
            // البحث عن الكرت
            $card = Card::where('card_number', $cardNumber)
                ->with(['storageLibrary.hiddenAlbum'])
                ->firstOrFail();

            // التحقق من صلاحية الكرت
            if (!$card->isValid()) {
                throw new \Exception('Card is not valid or has expired');
            }

            // التحقق من أن الكرت غير مستخدم
            if ($card->holder_id) {
                throw new \Exception('Card has already been redeemed');
            }

            // التحقق من ربط الكرت بمكتبة تخزين
            if (!$card->storage_library_id) {
                throw new \Exception('Card is not linked to any storage library');
            }

            // ربط المستخدم بالكرت
            $card->update([
                'holder_id' => $user->id,
                'activation_date' => now(),
                'last_used' => now(),
            ]);

            // المستخدم الآن له وصول للألبوم المخفي عبر الكرت
            // لا حاجة لربط إضافي - العلاقة موجودة عبر card_albums

            DB::commit();

            Log::info('Card redeemed successfully', [
                'card_id' => $card->card_id,
                'user_id' => $user->id,
                'library_id' => $card->storage_library_id,
            ]);

            return $card->fresh()->loadCardRelations();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to redeem card', [
                'user_id' => $user->id,
                'card_number' => $cardNumber,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }
}
