<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ empty($post->id) ? __('Edit User') : __('Add User') }}
        </h2>
    </x-slot>

    <div class="py-12">

        @if (!empty(\Session::has('type')) || $errors->any())
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-center min-w-full mb-4">
                    <div class="flex items-center {{ (!empty(\Session::has('type')) && \Session::get('type') == 'error') || $errors->any() ? 'bg-red-500' : 'bg-green-500' }} min-w-full text-white text-sm font-bold px-4 py-3" role="alert">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z"/>
                        </svg>
                        <p>{{ (\Session::has('message')) ? \Session::get('message') : $errors->first() }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {!! Form::open(['method' => empty($user->id) ? 'post' : 'put', 'url' => empty($user->id) ? url("/users") : url("/users/$user->id"), 'id' => 'editForm','class'=>'needs-validation','novalidate']) !!}

                    <div>
                        <x-label for="name" :value="__('Name')" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="$user->name" required autofocus />
                    </div>
                    <div class="mt-3">
                        <x-label for="email" :value="__('Email')" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="$user->email" required autofocus />
                    </div>
                    <div>
                        <x-label for="name" class="mt-3" :value="__('Is Admin')" />
                        <x-checkbox id="admin" class="block mt-1" name="admin" :checked="$user->admin"/>
                    </div>
                    @if (empty($user->id))
                        <div class="mt-4">
                            <x-label for="password" :value="__('Password')" />
                            <x-input id="password" class="block mt-1 w-full"
                                     type="password"
                                     name="password"
                                     required autocomplete="new-password" />
                        </div>
                        <div class="mt-4">
                            <x-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-input id="password_confirmation" class="block mt-1 w-full"
                                     type="password"
                                     name="password_confirmation" required />
                        </div>
                    @endif
                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-3">
                            {{ empty($user->id) ? __('Add') : __('Update') }}
                        </x-button>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
