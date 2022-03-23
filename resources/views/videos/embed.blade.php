

<x-embed-layout>

    <div class="relative w-full h-full max-w-full max-h-full bg-black">

        <x-player
            class="w-full max-w-full h-full max-h-full"
            {{-- id="player" --}}
            :cover="$thumbnail"
            :hls="$hls_playlist"
            :title="$title">
        </x-player>

    </div>

</x-embed-layout>
