@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-utec-gray-medium focus:border-utec-primary-light focus:ring-utec-primary-light rounded-md shadow-sm']) }}>