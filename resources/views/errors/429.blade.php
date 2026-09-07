@extends('errors.layout')

@section('code', '429')
@section('title', 'অনেক বেশি অনুরোধ')
@section('message', 'অল্প সময়ে অনেকবার অনুরোধ করা হয়েছে। কিছুক্ষণ অপেক্ষা করে আবার চেষ্টা করুন।')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <path d="M3 12h3.5l2.5 6 4-12 2.5 6H21" />
    </svg>
@endsection
