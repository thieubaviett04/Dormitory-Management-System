@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow-sm border border-border p-6">
    <h1 class="text-2xl font-semibold mb-2">Student Dashboard</h1>
    <p class="text-muted-foreground">Xin chào, {{ Auth::user()->name }}!</p>
</div>
@endsection
