@props(['title', 'action' => false])

<th {{ $attributes->merge(['class' => ' px-6 py-4 bg-cool-gray-50 leading-4 font-semibold text-cool-gray-500 uppercase tracking-wider']) }}>
    {{ !$action ? $title : $slot }}
</th>