<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\{StoreSchoolRequest, UpdateSchoolRequest};
use App\Services\Admin\UserService;
use App\Models\{School, LookupValue};
use App\Traits\{HasApiResponse, MapsRoleToType};
use Illuminate\Http\{Request, JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\{Log, Storage};
use Illuminate\View\View;

class SchoolController extends Controller
{
    use HasApiResponse, MapsRoleToType;

    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of schools.
     */
    public function index(Request $request): View|JsonResponse
    {
        $schools = School::with(['user.status', 'type', 'level'])
            ->filter($request->only('search', 'status_id', 'school_type_id', 'school_level_id', 'city'))
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        $data = [
            'schools' => $schools,
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))->get(),
            'types' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_TYPE'))->get(),
            'levels' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_LEVEL'))->get(),
        ];

        if ($request->ajax()) {
            return $this->paginatedResponse($schools, 'schools');
        }

        return view('spa.schools.index', $data);
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        try {
            $data = $this->prepareSchoolData($request->validated());
            $user = $this->userService->createSpecialUser($data, 'school');

            return $this->successResponse(
                ['school' => $user->school->load(['user.status', 'type', 'level'])],
                'تم إنشاء المدرسة بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error creating school: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء إنشاء المدرسة', 500);
        }
    }

    /**
     * Show the form for editing the specified school.
     */
    public function edit(School $school): View|JsonResponse
    {
        $school->load(['user', 'type', 'level']);

        $data = [
            'school' => $school,
            'statuses' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))->get(),
            'schoolTypes' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_TYPE'))->get(),
            'schoolLevels' => LookupValue::whereHas('master', fn($q) => $q->where('code', 'SCHOOL_LEVEL'))->get(),
        ];

        if (request()->ajax()) {
            return $this->successResponse($data);
        }

        return view('spa.schools.edit', $data);
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, School $school): JsonResponse|RedirectResponse
    {
        try {
            $data = $this->prepareSchoolData($request->validated(), $school);

            if (!$school->user) {
                return $this->handleError(new \Exception('School user not found'), $request, 'المستخدم المرتبط بالمدرسة غير موجود');
            }

            $updatedUser = $this->userService->updateSpecialUser($school->user, $data);

            if ($request->wantsJson()) {
                return $this->successResponse(
                    ['school' => $school->fresh()->load(['user.status', 'type', 'level'])],
                    'تم تحديث بيانات المدرسة بنجاح'
                );
            }

            return redirect()->back()->with('success', 'تم تحديث بيانات المدرسة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error updating school: ' . $e->getMessage());
            return $this->handleError($e, $request, 'حدث خطأ أثناء تحديث المدرسة');
        }
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school): JsonResponse|RedirectResponse
    {
        try {
            if ($school->user) {
                $this->userService->deleteUser($school->user);
            } else {
                $school->delete();
            }

            if (request()->wantsJson()) {
                return $this->successResponse([], 'تم حذف المدرسة بنجاح');
            }

            return redirect()->route('spa.schools')->with('success', 'تم حذف المدرسة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error deleting school: ' . $e->getMessage());
            return $this->handleError($e, request(), 'حدث خطأ أثناء حذف المدرسة');
        }
    }

    /**
     * Get school statistics.
     */
    public function statistics(School $school): JsonResponse
    {
        try {
            $stats = $this->getSchoolStatistics($school);

            return $this->successResponse(
                ['statistics' => $stats],
                'تم استرجاع إحصائيات المدرسة بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error fetching school statistics: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء استرجاع إحصائيات المدرسة', 500);
        }
    }

    /**
     * Search for schools.
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $search = $request->get('search', '');
            $schools = School::with(['user.status', 'type', 'level'])
                ->where(function($query) use ($search) {
                    $query->where('description', 'like', "%{$search}%")
                          ->orWhere('city', 'like', "%{$search}%")
                          ->orWhereHas('user', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                })
                ->limit(10)
                ->get(['school_id', 'user_id', 'description', 'city']);

            return $this->successResponse(['schools' => $schools]);
        } catch (\Exception $e) {
            Log::error('Error searching schools: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء البحث عن المدارس', 500);
        }
    }

    /**
     * Update school logo.
     */
    public function updateLogo(Request $request, School $school): JsonResponse
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            // Delete old logo if exists
            if ($school->logo) {
                Storage::delete('public/' . $school->logo);
            }

            // Upload new logo
            $path = $request->file('logo')->store('schools/logos', 'public');
            $school->update(['logo' => $path]);

            return $this->successResponse(
                ['logo_url' => $school->logo_url],
                'تم تحديث شعار المدرسة بنجاح'
            );
        } catch (\Exception $e) {
            Log::error('Error updating school logo: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء تحديث شعار المدرسة', 500);
        }
    }

    /**
     * Remove school logo.
     */
    public function removeLogo(School $school): JsonResponse
    {
        try {
            if ($school->logo) {
                Storage::delete('public/' . $school->logo);
                $school->update(['logo' => null]);
            }

            return $this->successResponse([], 'تم حذف شعار المدرسة بنجاح');
        } catch (\Exception $e) {
            Log::error('Error removing school logo: ' . $e->getMessage());
            return $this->errorResponse('حدث خطأ أثناء حذف شعار المدرسة', 500);
        }
    }

    private function prepareSchoolData(array $validatedData, ?School $school = null): array
    {
        $data = $validatedData;

        // إضافة username تلقائي إذا لم يكن موجوداً
        if (!isset($data['username']) && isset($data['full_name'])) {
            $data['username'] = $this->generateUsername($data['full_name'], 'school');
        }

        // إضافة password تلقائي إذا لم يكن موجوداً (للإنشاء فقط)
        if (!isset($data['password']) && !$school) {
            $data['password'] = $this->generateDefaultPassword('school');
        }

        // نسخ description من full_name إذا لم يكن موجوداً
        if (!isset($data['description']) && isset($data['full_name'])) {
            $data['description'] = $data['full_name'];
        }

        // معالجة رفع الصورة إذا وجدت
        if (isset($validatedData['logo']) && $validatedData['logo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logo'] = $this->handleLogoUpload($validatedData['logo'], $school);
        } elseif (isset($validatedData['logo'])) {
            // الحفاظ على الصورة الحالية إذا لم يتم رفع صورة جديدة
            unset($data['logo']);
        }

        return $data;
    }

    private function handleLogoUpload(\Illuminate\Http\UploadedFile $logo, ?School $school = null): string
    {
        // حذف الصورة القديمة إذا وجدت
        if ($school && $school->logo) {
            Storage::delete('public/' . $school->logo);
        }

        // رفع الصورة الجديدة
        return $logo->store('schools/logos', 'public');
    }

    private function generateUsername(string $name, string $prefix): string
    {
        $cleanName = preg_replace('/[^a-z0-9]/', '_', strtolower($name));
        return $prefix . '_' . substr($cleanName, 0, 20) . '_' . rand(100, 999);
    }

    private function generateDefaultPassword(string $type = 'school'): string
    {
        $types = [
            'school' => 'School@',
            'studio' => 'Studio@',
            'user' => 'User@'
        ];

        $prefix = $types[$type] ?? 'User@';
        return $prefix . rand(1000, 9999);
    }

    private function getSchoolStatistics(School $school): array
    {
        return [
            'basic_info' => [
                'name' => $school->user->name ?? $school->description,
                'type' => $school->type->name ?? null,
                'level' => $school->level->name ?? null,
                'city' => $school->city,
                'status' => $school->user->status->name ?? null,
                'profile_completion' => $school->getProfileCompletionPercentage(),
            ],
            'counts' => [
                'albums_count' => $school->albums()->count(),
                'cards_count' => $school->cards()->count(),
                'storage_libraries_count' => $school->storageLibraries()->count(),
            ],
            'storage_info' => $this->getSchoolStorageInfo($school),
            'subscription_info' => [
                'has_active_subscription' => $school->hasActiveSubscription(),
                'subscription_details' => $school->user->activeSubscription ?? null,
            ],
        ];
    }

    private function getSchoolStorageInfo(School $school): array
    {
        $storageLibraries = $school->storageLibraries;
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
