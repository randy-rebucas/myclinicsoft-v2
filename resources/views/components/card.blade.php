<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl ring-1 ring-gray-200']) }}>
    @if (isset($header))
        <div class="px-6 py-4 border-b border-gray-200">
            {{ $header }}
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
