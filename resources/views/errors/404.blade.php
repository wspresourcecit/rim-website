@extends('errors.layout')

@section('code', '404')
@section('title', 'পেজটি খুঁজে পাওয়া যায়নি')
@section('message', 'আপনি যে পেজটি খুঁজছেন সেটি সরিয়ে ফেলা হয়েছে, নাম বদলে গেছে, অথবা কখনো ছিল না।')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <circle cx="10.5" cy="10.5" r="6.5" />
        <path d="m21 21-5.2-5.2" />
    </svg>
@endsection
