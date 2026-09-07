@extends('errors.layout')

@section('code', '503')
@section('title', 'সাময়িকভাবে অফলাইন')

@section('message')
    {!! $maintenanceMessage ?? 'রক্ষণাবেক্ষণের কাজ চলছে। আমরা খুব শিগগিরই ফিরে আসছি — একটু পর আবার চেষ্টা করুন।' !!}
@endsection

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <path d="M14.7 6.3a4 4 0 0 0-5.4 5.4L4 17l3 3 5.3-5.3a4 4 0 0 0 5.4-5.4l-2.6 2.6-2.4-2.4 2.6-2.6Z" />
    </svg>
@endsection
