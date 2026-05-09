@props(['active'])

@php
$classes = ($active ?? false)
? 'block w-full ps-3 pe-4 py-2 border-l-4 border-utec-primary text-start text-base font-medium text-utec-primary bg-utec-primary-soft focus:outline-none focus:text-utec-primary-dark focus:bg-utec-primary-soft focus:border-utec-primary-dark transition duration-150 ease-in-out'
: 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-utec-primary hover:bg-utec-primary-soft hover:border-utec-primary-light focus:outline-none focus:text-utec-primary focus:bg-utec-primary-soft focus:border-utec-primary-light transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>