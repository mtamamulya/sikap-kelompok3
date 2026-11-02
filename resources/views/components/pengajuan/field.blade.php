@props(['label', 'required' => false, 'optional' => false, 'hint' => null, 'name' => null])
<div {{ $attributes->merge(['class' => 'flex min-w-0 flex-col gap-1.5']) }}>
    <label class="text-[13px] font-semibold leading-5 text-[#374151]">
        {{ $label }}
        @if ($required)<span class="text-[#FF3F3F]"> *</span>@endif
        @if ($optional)<span class="font-normal text-[#6B7280]"> (opsional)</span>@endif
    </label>
    {{ $slot }}
    @if ($hint)<p class="text-xs leading-[18px] text-[#6B7280]">{{ $hint }}</p>@endif
    @if ($name)@error($name)<p class="text-xs text-[#FF3F3F]">{{ $message }}</p>@enderror @endif
</div>