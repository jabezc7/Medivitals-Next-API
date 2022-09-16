@component('mail::layout')

{{-- Header --}}
@slot('header')
@endslot

{{-- Body --}}
<p><strong>Reset Password</strong></p>
<p>Hello {{ $user->first }} {{ $user->last }},</p>
<p>You have requested a password reset for your account.</p>

@component('mail::button', ['url' => env('SPA_URL') . '/reset-password?token=' . $token, 'color' => 'primary'])
Click Here To Reset Password
@endcomponent

<p>If you did not request a password reset, please ignore this message.</p>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
Powered by Niftee
@endcomponent
@endslot

@endcomponent
