@if(file_exists(public_path('images/LOGO.png')))
    <img src="{{ asset('images/LOGO.png') }}"
         alt="UTEC"
         {{ $attributes->merge(['class' => 'h-14 w-auto object-contain']) }}>
@else
    <div {{ $attributes->merge(['class' => 'w-14 h-14 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center']) }}>
        <span class="text-white text-xl font-semibold">UV</span>
    </div>
@endif