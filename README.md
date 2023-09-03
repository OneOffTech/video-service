
## About OneOffTech's Video Service

OneOffTech Video Service (OVS) is a web application designed to self-host public videos.

The intended use case is a small scale, privacy first, self-hosted Youtube or Vimeo for
your company to deliver public videos on your website or blog.

- Authenticated space to prepare video publication.
- Embeddable player with streaming support to use in websites/blogs.
- Public video listing and playback.
- Pseudo-streaming using common media formats, e.g. mp4/h264, webm/vp9
- [HTTP Live Streaming (HLS)](https://developer.apple.com/streaming/) 
to adapt to clients resources and environment.


> 🚧 **OVS is a work in progress**. Please, try it out and report back using 
[Discussions](https://github.com/OneOffTech/video-service/discussions).


## Getting started

OneOffTech's Video Service primary usage is via web browsers. Considering the focus of self-hosting the 
application is packaged via a Docker image that can be used directly via the Docker runtime, on Kubernetes
or any other container services.

> The following usage example assumes that you have [Docker](https://www.docker.com/),
[Docker Compose](https://docs.docker.com/compose/) and a [MariaDB 10.6](https://mariadb.org/) server.

_We still have to complete the bare minimum usage documentation_

## Developing 

OneOffTech Video Service (OVS) is built using the [Laravel framework](https://laravel.com/). 
[Livewire](https://laravel-livewire.com/) is used to deliver dynamic
components, while [Tailwind CSS](https://tailwindcss.com/) powers
the UI styling. Video elaboration stands on the shoulders of the great
[FFmpeg](https://ffmpeg.org/) and [Laravel FFMpeg](https://github.com/protonemedia/laravel-ffmpeg).

Given the selected stack OVS requires:

- [PHP 8.1](https://www.php.net/) or above
- [Composer 2](https://getcomposer.org/)
- [NodeJS](https://nodejs.org/en/) version 16 or above with
[Yarn](https://classic.yarnpkg.com/en/docs/install) (v1.x) package manager
- [MariaDB](https://mariadb.org/) version 10.6 or above
- [Docker](https://www.docker.com/)
- [FFmpeg](https://ffmpeg.org/download.html) binaries available. You will need both `ffmpeg` and `ffprobe` binaries.


## Contributing

Thank you for considering contributing to the Video Service!
Have a look at the [contribution guide](./.github/CONTRIBUTING.md).


## Security Vulnerabilities

If you discover a security vulnerability within Video Service, please send an e-mail to
OneOffTech Security via [security@oneofftech.xyz](mailto:security@oneofftech.xyz).
All security vulnerabilities will be promptly addressed.

## License

The OneOffTech Video Service is open-sourced software licensed under the 
[MIT license](https://opensource.org/licenses/MIT).
