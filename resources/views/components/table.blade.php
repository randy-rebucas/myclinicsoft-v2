@props(['for'])

<table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-cool-gray-200']) }} id="{{ $for }}">
    {{ $slot }}
</table>
