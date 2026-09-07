@extends('errors.layout')

@section('code', '500')
@section('title', 'কিছু একটা সমস্যা হয়েছে')
@section('message', 'সাময়িক একটি সমস্যার কারণে পেজটি লোড করা যায়নি। আমরা বিষয়টি দেখছি — কিছুক্ষণ পর আবার চেষ্টা করুন।')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h16.9a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z" />
        <path d="M12 9v4" />
        <path d="M12 17h.01" />
    </svg>
@endsection
