@extends('errors.layout')

@section('code', '419')
@section('title', 'সেশনের মেয়াদ শেষ হয়ে গেছে')
@section('message', 'নিরাপত্তার কারণে আপনার সেশনের মেয়াদ শেষ হয়েছে। পেজটি রিফ্রেশ করে আবার চেষ্টা করুন।')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="8.5" />
        <path d="M12 7v5l3.5 2" />
    </svg>
@endsection
