@component('mail::layout')

{{-- Header --}}
@slot('header')
@endslot

{{-- Body --}}
<p>{{ $message }}</p>

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
Powered by Medivitals
@endcomponent
@endslot

@endcomponent
