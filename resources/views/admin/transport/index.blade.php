@extends('layouts.dashboard')

@section('page-title', 'النقل المدرسي')
@section('page-description', 'إدارة الحافلات والمسارات')

@section('dashboard-content')
<button onclick="openBusModal()" class="mb-6 px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition flex items-center gap-2">
    <i class="fas fa-plus"></i> إضافة حافلة
</button>

<!-- الحافلات -->
<h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
    <i class="fas fa-bus text-indigo-500"></i> الحافلات
</h3>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @forelse($buses as $bus)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden card-hover">
            <div class="gradient-bg p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bus text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-white">حافلة {{ $bus->bus_number }}</h3>
                            <p class="text-xs text-white/80">{{ $bus->plate_number ?? 'رقم اللوحة غير محدد' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.transport.buses.destroy', $bus) }}" method="POST" onsubmit="return confirm('حذف الحافلة؟')">
                        @csrf @method('DELETE')
                        <button class="text-white/70 hover:text-white"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">السائق</span><span class="font-semibold">{{ $bus->driver_name ?? 'غير محدد' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المشرف</span><span class="font-semibold">{{ $bus->supervisor_name ?? 'غير محدد' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">السعة</span><span class="font-semibold">{{ $bus->capacity }} مقعد</span></div>
                <div class="flex justify-between"><span class="text-gray-500">المسارات</span><span class="font-semibold text-indigo-600">{{ $bus->routes_count }}</span></div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500">
            <i class="fas fa-bus text-5xl text-gray-300 mb-3 block"></i> لا توجد حافلات
        </div>
    @endforelse
</div>

<!-- المسارات -->
<h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
    <i class="fas fa-route text-indigo-500"></i> المسارات
</h3>
<button onclick="openRouteModal()" class="mb-4 px-4 py-2 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
    <i class="fas fa-plus ml-1"></i> إضافة مسار
</button>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @forelse($routes as $route)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-gray-800">{{ $route->name }}</h4>
                    <p class="text-xs text-gray-500">حافلة {{ $route->bus->bus_number ?? '-' }}</p>
                </div>
                <form action="{{ route('admin.transport.routes.destroy', $route) }}" method="POST" onsubmit="return confirm('حذف المسار؟')">
                    @csrf @method('DELETE')
                    <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </form>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between text-sm mb-3">
                    <span class="text-gray-500">الطلاب المسجلون</span>
                    <span class="font-semibold text-indigo-600">{{ $route->transportStudents->count() }}</span>
                </div>
                <button onclick="openAssignModal({{ $route->id }}, '{{ addslashes($route->name) }}')" 
                    class="w-full px-4 py-2 text-sm bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition">
                    <i class="fas fa-user-plus ml-1"></i> ربط طالب
                </button>
                
                @if($route->transportStudents->count())
                    <div class="mt-3 space-y-2 max-h-40 overflow-y-auto">
                        @foreach($route->transportStudents as $ts)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg text-sm">
                                <span>{{ $ts->student->name }}</span>
                                <div class="flex items-center gap-2">
                                    @if($ts->pickup_point)<span class="text-xs text-gray-500">{{ $ts->pickup_point }}</span>@endif
                                    <form action="{{ route('admin.transport.students.remove', $ts) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-8 text-center text-gray-500">
            <i class="fas fa-route text-5xl text-gray-300 mb-3 block"></i> لا توجد مسارات
        </div>
    @endforelse
</div>

<!-- Modal حافلة -->
<div id="busModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeBusModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4">إضافة حافلة</h3>
            <form action="{{ route('admin.transport.buses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="bus_number" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="رقم الحافلة *">
                    <input type="text" name="plate_number" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="رقم اللوحة">
                </div>
                <input type="number" name="capacity" min="1" value="40" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="السعة">
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="driver_name" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="اسم السائق">
                    <input type="text" name="driver_phone" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="هاتف السائق">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="supervisor_name" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="اسم المشرف">
                    <input type="text" name="supervisor_phone" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="هاتف المشرف">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">حفظ الحافلة</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal مسار -->
<div id="routeModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeRouteModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4">إضافة مسار</h3>
            <form action="{{ route('admin.transport.routes.store') }}" method="POST" class="space-y-4">
                @csrf
                <select name="bus_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر الحافلة</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}">حافلة {{ $bus->bus_number }}</option>
                    @endforeach
                </select>
                <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="اسم المسار *">
                <div class="grid grid-cols-2 gap-4">
                    <input type="time" name="pickup_time" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <input type="time" name="dropoff_time" class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                </div>
                <textarea name="description" rows="2" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="الوصف"></textarea>
                <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white rounded-xl">حفظ المسار</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal ربط طالب -->
<div id="assignModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeAssignModal()"></div>
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4">ربط طالب بالمسار</h3>
            <form id="assignForm" action="{{ route('admin.transport.students.assign') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="route_id" id="assignRouteId">
                <p id="assignRouteName" class="text-sm text-gray-600"></p>
                <select name="student_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl">
                    <option value="">اختر الطالب</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="pickup_point" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="نقطة الاستلام">
                    <input type="text" name="dropoff_point" class="w-full px-4 py-3 border border-gray-200 rounded-xl" placeholder="نقطة التسليم">
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-green-600 text-white rounded-xl">ربط الطالب</button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openBusModal(){document.getElementById('busModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeBusModal(){document.getElementById('busModal').classList.add('hidden');document.body.style.overflow='';}
function openRouteModal(){document.getElementById('routeModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeRouteModal(){document.getElementById('routeModal').classList.add('hidden');document.body.style.overflow='';}
function openAssignModal(id,name){document.getElementById('assignRouteId').value=id;document.getElementById('assignRouteName').textContent='المسار: '+name;document.getElementById('assignModal').classList.remove('hidden');document.body.style.overflow='hidden';}
function closeAssignModal(){document.getElementById('assignModal').classList.add('hidden');document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeBusModal();closeRouteModal();closeAssignModal();}});
</script>
@endpush
@endsection
