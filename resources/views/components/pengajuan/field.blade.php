@props([
    'label',
    'name',
    'hint' => null,
    'required' => false,
    'optional' => false,
])

<label {{ $attributes->merge(['class' => 'flex flex-col gap-1.5 text-[13px] font-medium text-[#374151]']) }}>
    <span>
        {{ $label }}
        @if ($required)
            <span class="text-[#EF4444]">*</span>
        @elseif ($optional)
            <span class="font-normal text-[#6B7280]">(Opsional)</span>
        @endif
    </span>
    @if ($hint)
        <span class="text-xs font-normal leading-[18px] text-[#6B7280]">{{ $hint }}</span>
    @endif
    {{ $slot }}
    @error($name)
        <span class="text-xs font-normal text-[#DC2626]">{{ $message }}</span>
    @enderror
</label>