@extends('layouts.dashboard')

@section('page-title', 'النقل المدرسي')
@section('page-description', 'متابعة النقل وتفاصيل الحافلات')

@section('dashboard-content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($students as $student)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-graduate text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white">{{ $student->name }}</h3>
                        <p class="text-xs text-white/80">{{ $student->classroom->full_name }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5">
                @forelse($student->transportAssignments as $assignment)
                    <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-green-800">
                                <i class="fas fa-route ml-1"></i> {{ $assignment->route->name }}
                            </span>
                            <span class="px-2 py-1 text-xs bg-green-600 text-white rounded-full">
                                حافلة {{ $assignment->route->bus->bus_number }}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">نقطة الاستلام</span>
                                <span class="font-semibold text-gray-800">{{ $assignment->pickup_point ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">نقطة التسليم</span>
                                <span class="font-semibold text-gray-800">{{ $assignment->dropoff_point ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">السائق</span>
                                <span class="font-semibold text-gray-800">{{ $assignment->route->bus->driver_name ?? 'غير محدد' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">هاتف السائق</span>
                                <span class="font-semibold text-gray-800" dir="ltr">{{ $assignment->route->bus->driver_phone ?? 'غير محدد' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-bus text-4xl text-gray-300 mb-3 block"></i>
                        لا يوجد نقل مسجل لهذا الطالب
                    </div>
                @endforelse
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-500">
            <i class="fas fa-user-graduate text-6xl text-gray-300 mb-4"></i>
            لا يوجد طلاب مسجلون
        </div>
    @endforelse
</div>
@endsection
