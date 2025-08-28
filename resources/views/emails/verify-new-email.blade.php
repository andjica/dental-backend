@component('mail::message')
# Confirm your new email

Hi {{ $user->name }},

Click the button below to verify your new email address:

@component('mail::button', ['url' => $verifyUrl])
Verify Email
@endcomponent

If you did not request this change, you can ignore this message.

Thanks,  
{{ config('app.name') }}
@endcomponent

