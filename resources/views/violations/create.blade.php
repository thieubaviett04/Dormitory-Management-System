@extends('layouts.app')

@section('title', 'Lập Biên bản Vi phạm')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end space-y-4 sm:space-y-0">
        <div>
            <h2 class="text-2xl font-semibold tracking-tight">Lập Biên bản Vi phạm</h2>
            <p class="text-sm text-muted-foreground mt-1">Ghi nhận vi phạm kỷ luật mới đối với sinh viên.</p>
        </div>
        
        <div>
            <a href="{{ route('violation.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                <i data-lucide="arrow-left" class="mr-2 h-4 w-4"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Form Container -->
    <div class="rounded-xl border bg-card text-card-foreground shadow-sm">
        <div class="p-6">
            <form action="{{ route('violation.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Sinh viên -->
                    <div class="space-y-2">
                        <label for="student_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Sinh viên vi phạm <span class="text-destructive">*</span></label>
                        <select id="student_id" name="student_id" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">-- Chọn sinh viên --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    SV #{{ $student->id }} - {{ $student->full_name ?? 'Chưa cập nhật tên' }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lỗi vi phạm -->
                    <div class="space-y-2">
                        <label for="violation_type_id" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Loại vi phạm <span class="text-destructive">*</span></label>
                        <select id="violation_type_id" name="violation_type_id" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">-- Chọn loại vi phạm --</option>
                            @foreach($violationTypes as $type)
                                <option value="{{ $type->id }}" {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} (Phạt: {{ number_format($type->fine_amount) }}đ)
                                </option>
                            @endforeach
                        </select>
                        @error('violation_type_id')
                            <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ngày vi phạm -->
                    <div class="space-y-2">
                        <label for="record_date" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Ngày vi phạm <span class="text-destructive">*</span></label>
                        <input type="date" id="record_date" name="record_date" value="{{ old('record_date', date('Y-m-d')) }}" required class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        @error('record_date')
                            <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Mô tả chi tiết -->
                <div class="space-y-2">
                    <label for="description" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Mô tả chi tiết sự việc</label>
                    <textarea id="description" name="description" rows="4" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" placeholder="Nhập mô tả chi tiết về hành vi vi phạm, thời gian, địa điểm cụ thể (nếu có)...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-destructive mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('violation.index') }}" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                        Hủy bỏ
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground shadow hover:bg-primary/90 h-10 px-4 py-2">
                        <i data-lucide="save" class="mr-2 h-4 w-4"></i> Lưu biên bản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
