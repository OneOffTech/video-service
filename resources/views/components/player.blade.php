@props(['id' => null, 'hls' => null, 'cover' => null, 'title' => null, 'disabled' => false])

@php
    $playerId = $id ?? 'pl-'.str()->random(10);
@endphp

<video {{ $attributes }} id="{{ $playerId }}" preload="none" playsinline controls data-poster="{{ $cover }}" data-plyr-config=''>

    {{-- Do not preload the video by default. More info on preload attribute refer to https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video#attr-preload --}}

    {{-- <source src="/path/to/video.mp4" type="video/mp4" />
    <source src="/path/to/video.webm" type="video/webm" /> --}}

    {{-- Captions are optional --}}
    {{-- <track kind="captions" label="English captions" src="/path/to/captions.vtt" srclang="en" default /> --}}
</video>

<script defer>
    document.addEventListener('DOMContentLoaded', () => {
        // For more options see: https://github.com/sampotts/plyr/#options
        // captions.update is required for captions to work with hls.js
        const source = '{{ $hls }}';
        let video = document.getElementById("{{$playerId}}");
        const player = new Plyr(video, {

            title: "{{ $title }}",
            previewThumbnails: { enabled: false, "src": "" },
            iconUrl: "{{ url('images/plyr.svg') }}",
            storage: { enabled: false, key: 'plyr'},

            controls: [
                'play-large', // The large play button in the center
                'rewind', // Rewind by the seek time (default 10 seconds)
                'play', // Play/pause playback
                'fast-forward', // Fast forward by the seek time (default 10 seconds)
                'progress', // The progress bar and scrubber for playback and buffering
                'current-time', // The current time of playback
                'duration', // The full duration of the media
                'mute', // Toggle mute
                'volume', // Volume control
                'captions', // Toggle captions
                'settings', // Settings menu
                'fullscreen', // Toggle fullscreen
            ],

            tooltips: { controls: false, seek: true },

            speed: { selected: 1, options: [0.5, 1, 1.5, 2] },

            captions: {active: true, update: true, language: 'en'}
        });
        
        if (!Hls.isSupported()) {
            video.src = source;
        } else {
            // For more Hls.js options, see https://github.com/video-dev/hls.js/
            const hls = new Hls();
            hls.loadSource(source);
            hls.attachMedia(video);
            window.hls = hls;
            
            // Handle changing captions
            player.on('languagechange', () => {
                // Caption support is still flaky. See: https://github.com/sampotts/plyr/issues/994
                setTimeout(() => hls.subtitleTrack = player.currentTrack, 50);
            });
        }
    });
</script>
