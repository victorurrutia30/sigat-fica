@props(['active'])

@php
$classes = ($active ?? false)
? 'inline-flex items-center px-1 pt-1 border-b-2 border-utec-primary text-sm font-medium leading-5 text-utec-primary focus:outline-none focus:border-utec-primary-dark transition duration-150 ease-in-out'
: 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-600 hover:text-utec-primary hover:border-utec-primary-light focus:outline-none focus:text-utec-primary focus:border-utec-primary-light transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>