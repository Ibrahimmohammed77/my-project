<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{StoreStudioRequest, UpdateStudioRequest};
use App\Services\Admin\UserService;
use App\Models\{Studio, LookupValue};
use App\Traits\{HasApiResponse, MapsRoleToType};
use Illuminate\Http\{Request, JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\{Log, Storage};
use Illuminate\View\View;

class StudioController extends Controller
{
    use HasApiResponse, MapsRoleToType;

    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of studios.
     */
    public function index(Request $request): View|JsonResponse
    {
        $studios = Studio::with('user.status')
            ->filter($request->only('search', 'status_id', 'city'))
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $data = [
            'studios' => $studios,
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))->get(),
        ];

        if ($request->ajax()) {
            return $this->paginatedResponse($studios, 'studios');
        }

        return view('spa.studios.index', $data);
    }

    /**
     * Store a newly created studio.
     */
    public function store(StoreStudioRequest $request): JsonResponse
    {
        try {
            $data = $this->prepareStudioData($request->validated());
            $user = $this->userService->createSpecialUser($data, 'studio');

            return $this->successResponse(
                ['studio' => $user->studio->load(['user.status'])],
                'تم إنشاء الاستوديو بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error creating studio: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء الاستوديو', 500);
        }
    }

    /**
     * Show the form for editing the specified studio.
     */
    public function edit(Studio $studio): View|JsonResponse
    {
        $studio->load(['user']);

        $data = [
            'studio' => $studio,
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))->get(),
        ];

        if (request()->wantsJson()) {
            return $this->successResponse($data);
        }

        return view('spa.studios.edit', $data);
    }

    /**
     * Update the specified studio.
     */
    public function update(UpdateStudioRequest $request, Studio $studio): JsonResponse|RedirectResponse
    {
        try {
            $data = $this->prepareStudioData($request->validated(), $studio);

            if (!$studio->user) {
                return $this->handleError(new \Exception('Studio user not found'), $request, 'المستخدم المرتبط بالاستوديو غير موجود');
            }

            $updatedUser = $this->userService->updateSpecialUser($studio->user, $data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['studio' => $studio->fresh()->load(['user.status'])],
                    'تم تحديث بيانات الاستوديو بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات الاستوديو بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating studio: ' . $e->getMessage());
            return $this->handleError($e, $request, 'حدث خطأ أثناء تحديث الاستوديو');
        }
    }

    /**
     * Remove the specified studio.
     */
    public function destroy(Studio $studio): JsonResponse|RedirectResponse
    {
        try {
            if ($studio->user) {
                $this->userService->deleteUser($studio->user);
            } else {
                $studio->delete();
            }

            if (request()->wantsJson()) {
                return $this->successResponse([], 'تم حذف الاستوديو بنجاح');
            }

            return redirect()->route('spa.studios')->with('success', 'تم حذف الاستوديو بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting studio: ' . $e->getMessage());
            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف الاستوديو');
        }
    }

    /**
     * Get studio statistics.
     */
    public function statistics(Studio $studio): JsonResponse
    {
        try {
            $stats = $this->getStudioStatistics($studio);

            return $this->successResponse(
                ['statistics' => $stats],
                'تم استرجاع إحصائيات الاستوديو بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error fetching studio statistics: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء استرجاع إحصائيات الاستوديو', 500);
        }
    }

    /**
     * Update studio logo.
     */
    public function updateLogo(Request $request, Studio $studio): JsonResponse
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // Delete old logo if exists
            if ($studio->logo) {
                Storage::delete('public/' . $studio->logo);
            }

            // Upload new logo
            $path = $request->file('logo')->store('studios/logos', 'public');
            $studio->update(['logo' => $path]);

            return $this->successResponse(
                ['logo_url' => $studio->logo_url],
                'تم تحديث شعار الاستوديو بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error updating studio logo: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث شعار الاستوديو', 500);
        }
    }

    /**
     * Remove studio logo.
     */
    public function removeLogo(Studio $studio): JsonResponse
    {
        try {
            if ($studio->logo) {
                Storage::delete('public/' . $studio->logo);
                $studio->update(['logo' => null]);
            }

            return $this->successResponse([], 'تم حذف شعار الاستوديو بنجاح');
        } catch (\Exception $e) {
            Log::error('Error removing studio logo: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف شعار الاستوديو', 500);
        }
    }

    private function prepareStudioData(array $validatedData, ?Studio $studio = null): array
    {
        $data = $validatedData;

        // إضافة username تلقائي إذا لم يكن موجوداً
        if (!isset($data['username']) && isset($data['full_name'])) {
            $data['username'] = $this->generateUsername($data['full_name'], 'studio');
        }

        // إضافة password تلقائي إذا لم يكن موجوداً (للإنشاء فقط)
        if (!isset($data['password']) && !$studio) {
            $data['password'] = $this->generateDefaultPassword('studio');
        }

        // نسخ description من full_name إذا لم يكن موجوداً
        if (!isset($data['description']) && isset($data['full_name'])) {
            $data['description'] = $data['full_name'];
        }

        // معالجة رفع الصورة إذا وجدت
        if (isset($validatedData['logo']) && $validatedData['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logo'] = $this->handleLogoUpload($validatedData['logo'], $studio);
        } elseif (isset($validatedData['logo'])) {
            // الحفاظ على الصورة الحالية إذا لم يتم رفع صورة جديدة
            unset($data['logo']);
        }

        return $data;
    }

    private function handleLogoUpload(\Illuminate\Http\UploadedFile $logo, ?Studio $studio = null): string
    {
        // حذف الصورة القديمة إذا وجدت
        if ($studio && $studio->logo) {
            Storage::delete('public/' . $studio->logo);
        }

        // رفع الصورة الجديدة
        return $logo->store('studios/logos', 'public');
    }

    private function generateUsername(string $name, string $prefix): string
    {
        $cleanName = preg_replace('/[^a-z0-9]/', '_', strtolower($name));
        return $prefix . '_' . substr($cleanName, 0, 20) . '_' . rand(100, 999);
    }

    private function generateDefaultPassword(string $type = 'studio'): string
    {
        $types = [
            'school' => 'School@',
            'studio' => 'Studio@',
            'user' => 'User@'
        ];

        $prefix = $types[$type] ?? 'User@';
        return $prefix . rand(1000, 9999);
    }

    private function getStudioStatistics(Studio $studio): array
    {
        return [
            'basic_info' => [
                'name' => $studio->user->name ?? $studio->description,
                'city' => $studio->city,
                'status' => $studio->user->status->name ?? null,
                'profile_completion' => $studio->getProfileCompletionPercentage(),
            ],
            'counts' => [
                'albums_count' => $studio->albums()->count(),
                'cards_count' => $studio->cards()->count(),
                'customers_count' => $studio->customers()->count(),
                'commissions_count' => $studio->commissions()->count(),
                'storage_libraries_count' => $studio->storageLibraries()->count(),
            ],
            'storage_info' => $this->getStudioStorageInfo($studio),
            'subscription_info' => [
                'has_active_subscription' => $studio->hasActiveSubscription(),
                'subscription_details' => $studio->user->activeSubscription ?? null,
            ],
            'commission_info' => $this->getStudioCommissionInfo($studio),
        ];
    }

    private function getStudioStorageInfo(Studio $studio): array
    {
        $storageLibraries = $studio->storageLibraries;
        $totalStorage = $storageLibraries->sum('storage_limit');
        $usedStorage = $storageLibraries->sum('used_space');

        return [
            'total_storage' => $totalStorage,
            'used_storage' => $usedStorage,
            'available_storage' => max(0, $totalStorage - $usedStorage),
            'usage_percentage' => $totalStorage > 0 ? round(($usedStorage / $totalStorage) * 100, 2) : 0,
            'libraries_count' => $storageLibraries->count(),
        ];
    }

    private function getStudioCommissionInfo(Studio $studio): array
    {
        $commissions = $studio->commissions;

        return [
            'total_commissions' => $commissions->count(),
            'total_amount' => $commissions->sum('amount'),
            'pending_commissions' => $commissions->where('status', 'pending')->count(),
            'completed_commissions' => $commissions->where('status', 'completed')->count(),
        ];
    }

    private function handleError(\Exception $e, Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return $this->errorResponse(
                $message,
                500,
                ['error' => config('app.debug') ? $e->getMessage() : null]
            );
        }

        return back()->with('error', $message)->withInput();
    }
}
