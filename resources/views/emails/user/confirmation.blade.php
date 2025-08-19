@extends('layouts.email')

@section('content')
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #0D47A1;">Thank You for Contacting Us, {{ $formData['firstName'] }}!</h2>
    
    <div style="margin-top: 20px; padding: 20px; background-color: #f7fafc; border-radius: 8px;">
        <p>We've received your message and our team will get back to you within 24-48 hours.</p>
        
        <p style="margin-top: 15px;">In the meantime, you might want to explore our services:</p>
        
        <div style="margin: 20px 0; text-align: center;">
            <a href="{{ $servicesLink }}" style="display: inline-block; padding: 12px 24px; background-color: #0D47A1; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">
                Explore Our Services
            </a>
        </div>
        
        <p>Here's what you told us:</p>
        <div style="margin-top: 10px; padding: 10px; background-color: #fff; border-radius: 4px;">
            <p><strong>Subject:</strong> {{ $formData['service'] ?? 'General Inquiry' }}</p>
            <p><strong>Your Message:</strong></p>
            {!! nl2br(e($formData['message'])) !!}
        </div>
    </div>
    
    <p style="margin-top: 20px; color: #718096;">
        If you didn't initiate this contact, please ignore this email.
    </p>
</div>
@endsection