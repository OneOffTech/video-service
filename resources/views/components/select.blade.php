@props(['selected' => null, 'disabled' => false, 'options' => []])

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) !!}>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" {{ $value === $selected ? 'selected' : ''}}>{{ $label }}</option>
    @endforeach
</select>
