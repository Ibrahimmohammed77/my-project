@extends('layouts.app')

@section('title', 'إدارة العملاء')

@section('content')
<div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="text-xl font-black text-gray-800">قائمة العملاء</h3>
            <p class="text-sm text-gray-500 mt-1">إدارة بيانات العملاء والمنتسبين للاستوديو</p>
        </div>
    </div>

    <div class="p-8">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase tracking-widest border-b border-gray-50">
                        <th class="py-4 px-4 font-black">العميل</th>
                        <th class="py-4 px-4 font-black">البريد الإلكتروني</th>
                        <th class="py-4 px-4 font-black">رقم الجوال</th>
                        <th class="py-4 px-4 font-black">تاريخ الانضمام</th>
                        <th class="py-4 px-4 font-black">الحالة</th>
                        <th class="py-4 px-4 font-black">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($customers ?? [] as $customer)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent font-bold">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ $customer->email }}</td>
                            <td class="py-4 px-4 text-sm text-gray-600">{{ $customer->phone }}</td>
                            <td class="py-4 px-4 text-sm text-gray-500">{{ $customer->created_at->format('Y/m/d') }}</td>
                            <td class="py-4 px-4">
                                <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-50 text-green-600 border border-green-100">نشط</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex gap-2">
                                    <button class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                                    <i class="fa-solid fa-users text-gray-300 text-3xl"></i>
                                </div>
                                <h4 class="text-gray-800 font-bold">لا يوجد عملاء حالياً</h4>
                                <p class="text-gray-500 text-sm mt-1">لم يتم تسجيل أي عملاء تابعين للاستوديو بعد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
