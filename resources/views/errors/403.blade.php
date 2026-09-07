@extends('errors.layout')

@section('code', '403')
@section('title', 'এই পেজে প্রবেশের অনুমতি নেই')
@section('message', 'দুঃখিত, এই অংশটি দেখার অনুমতি আপনার নেই।')

@section('icon')
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round" aria-hidden="true">
        <rect x="4.5" y="10.5" width="15" height="10" rx="2" />
        <path d="M8 10.5V7a4 4 0 0 1 8 0v3.5" />
        <path d="M12 14.5v2.5" />
    </svg>
@endsection
