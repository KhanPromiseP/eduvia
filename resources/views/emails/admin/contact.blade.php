@extends('layouts.email')

@section('content')
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #0D47A1;">New Contact Form Submission</h2>
    
    <div style="margin-top: 20px; padding: 20px; background-color: #f7fafc; border-radius: 8px;">
        <p><strong>From:</strong> {{ $formData['firstName'] }} {{ $formData['lastName'] ?? '' }} &lt;{{ $formData['email'] }}&gt;</p>
        
        @if(isset($formData['service']))
        <p><strong>Service Interest:</strong> {{ $formData['service'] }}</p>
        @endif
        
        <p><strong>Message:</strong></p>
        <div style="margin-top: 10px; padding: 10px; background-color: #fff; border-radius: 4px;">
            {!! nl2br(e($formData['message'])) !!}
        </div>
    </div>
</div>
@endsection