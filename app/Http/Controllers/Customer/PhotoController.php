<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Photo;
use App\Models\StorageLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    /**
     * Upload a photo (sent to pending for studio review).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Find the library assigned to this user
        $library = StorageLibrary::where('user_id', $user->id)->first();

        if (!$library) {
            return response()->json(['success' => false, 'message' => 'لم يتم تخصيص مكتبة تخزين لك بعد'], 403);
        }

        $validated = $request->validate([
            'album_id' => 'required|exists:albums,album_id',
            'photo' => 'required|image|max:10240', // 10MB max
            'caption' => 'nullable|string|max:255',
        ]);

        $album = Album::where('album_id', $validated['album_id'])
            ->where('storage_library_id', $library->storage_library_id)
            ->firstOrFail();

        // Check storage limit
        $file = $request->file('photo');
        $size = $file->getSize();

        if ($library->available_storage < $size) {
             return response()->json(['success' => false, 'message' => 'مساحة التخزين غير كافية'], 422);
        }

        // Store file
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('photos/library_' . $library->storage_library_id, $storedName, 'public');

        $photo = Photo::create([
            'album_id' => $album->album_id,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'file_path' => 'public/' . $path,
            'file_size' => $size,
            'mime_type' => $file->getMimeType(),
            'caption' => $validated['caption'],
            'review_status' => Photo::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع الصورة بنجاح وهي قيد المراجعة من قبل الاستوديو',
            'data' => $photo
        ]);
    }
}
