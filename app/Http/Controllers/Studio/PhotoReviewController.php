<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\UseCases\Studio\Photo\ReviewPhotoUseCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhotoReviewController extends Controller
{
    protected $reviewPhotoUseCase;

    public function __construct(ReviewPhotoUseCase $reviewPhotoUseCase)
    {
        $this->reviewPhotoUseCase = $reviewPhotoUseCase;
    }

    /**
     * List pending photos for review.
     */
    public function pending(Request $request)
    {
        $studio = Auth::user()->studio;
        $photos = Photo::pending()
            ->whereHas('album.storageLibrary', function($q) use ($studio) {
                $q->where('studio_id', $studio->studio_id);
            })
            ->with(['album', 'album.storageLibrary.user'])
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $photos
            ]);
        }

        return view('studio.photo-review.pending', compact('photos'));
    }

    /**
     * Review a photo (approve or reject).
     */
    public function review(Request $request, $photoId)
    {
        $studio = Auth::user()->studio;

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
        ]);

        try {
            $photo = $this->reviewPhotoUseCase->execute(
                $studio, 
                $photoId, 
                $validated['status'], 
                $validated['rejection_reason'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة مراجعة الصورة بنجاح',
                'data' => $photo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
