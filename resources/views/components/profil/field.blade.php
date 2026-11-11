{{-- resources/views/components/profil/field.blade.php --}}
@props(['label', 'value' => null, 'detail'])

<div class="flex min-w-0 flex-1 basis-64 flex-col gap-1.5 rounded-lg bg-[#F9FAFB] p-3">
    <div class="text-base font-medium tracking-[-0.18px] text-[#6B7280]">{{ $label }}</div>
    @if ($slot->isEmpty())
        <div class="break-words text-xl font-semibold leading-7 text-[#111827]">{{ $value }}</div>
    @else
        {{ $slot }}
    @endif
    <div class="text-xs tracking-[-0.12px] text-[#6B7280]">{{ $detail }}</div>
</div>