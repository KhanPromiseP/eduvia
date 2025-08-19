New Contact Form Submission

From: {{ $formData['firstName'] }} {{ $formData['lastName'] ?? '' }} <{{ $formData['email'] }}>
Service Interest: {{ $formData['service'] ?? 'Not specified' }}

Message:
{{ $formData['message'] }}

This email was sent from the {{ config('app.name') }} contact form.