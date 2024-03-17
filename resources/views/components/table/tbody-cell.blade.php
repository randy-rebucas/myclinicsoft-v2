@props(['item', 'action' => false])

<td {{ $attributes->merge(['class' => ' px-6 py-2 whitespace-nowrap text-sm leading-5 text-cool-gray-500']) }}>
    {{ !$action ? $item : $slot }}
</td>
