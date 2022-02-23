<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <p class="text-center">Welcome to {{ config('app.name') }}</p>
    </x-auth-card>
</x-guest-layout>
