@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'input-error-lama']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif