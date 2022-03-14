<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add a new video') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <x-auth-validation-errors class="mb-4" :errors="$errors" />

                    <form method="POST" enctype="multipart/form-data" action="{{ route('videos.store') }}" class="space-y-3">
                    
                        @csrf

                        {{-- file --}}
                        <div>
                            <x-label for="file" :value="__('File to upload')" />

                            <input id="file" class="block mt-1 w-full" type="file" name="file" :value="old('file')" accept="video/mp4,.mp4" required />
                        </div>

                        {{-- title --}}
                        <div>
                            <x-label for="title" :value="__('Title')" />

                            <x-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                        </div>

                        {{-- description --}}
                        <div>
                            <x-label for="description" :value="__('Description')" />

                            <x-textarea id="description" class="block mt-1 w-full" name="description">{{old('description')}}</x-textarea>
                        </div>
                        
                        {{-- language --}}
                        <div>
                            <x-label for="language" :value="__('Language')" />

                            <x-select id="language" class="block mt-1 w-full" name="language" :selected="old('language')" 
                                :options="['en' => 'English']" />
                        </div>
                        
                        {{-- license --}}
                        <div>
                            <x-label for="license" :value="__('License')" />

                            <x-input id="license" class="block mt-1 w-full" type="text" name="license" :value="old('license')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-button>
                                {{ __('Create video') }}
                            </x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
