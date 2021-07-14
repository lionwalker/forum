<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="grid grid-cols-3 gap-4">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-row m-auto bg-gradient-to-r from-purple-700 via-purple-800 to-purple-900 p-6 gap-8 rounded-lg border-2 border-purple-500">
                            <div class="my-auto">
                                <div class="text-lg text-purple-300">TOTAL POSTS</div>
                                <div class="text-4xl text-purple-100">{{ $counts[0]->all_posts }}</div>
                            </div>
                            <div class="text-purple-300 my-auto bg-gradient-to-l from-purple-700 via-purple-800 to-purple-900 rounded-full p-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M5.5 16a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 16h-8z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-row m-auto bg-gradient-to-r from-purple-700 via-purple-800 to-purple-900 p-6 gap-8 rounded-lg border-2 border-purple-500">
                            <div class="my-auto">
                                <div class="text-lg text-purple-300">APPROVED</div>
                                <div class="text-4xl text-purple-100">{{ $counts[0]->approved_count }}</div>
                            </div>
                            <div class="text-purple-300 my-auto bg-gradient-to-l from-purple-700 via-purple-800 to-purple-900 rounded-full p-4">
                                <svg class="h-12 w-12" viewBox="0 0 32 32" aria-hidden="true" fill="currentColor">
                                    <path d="M5 16.577l2.194-2.195 5.486 5.484L24.804 7.743 27 9.937l-14.32 14.32z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-row m-auto bg-gradient-to-r from-purple-700 via-purple-800 to-purple-900 p-6 gap-8 rounded-lg border-2 border-purple-500">
                            <div class="my-auto">
                                <div class="text-lg text-purple-300">NOT APPROVED</div>
                                <div class="text-4xl text-purple-100">{{ $counts[0]->not_approved_count }}</div>
                            </div>
                            <div class="text-purple-300 my-auto bg-gradient-to-l from-purple-700 via-purple-800 to-purple-900 rounded-full p-4">
                                <svg class="h-12 w-12" viewBox="0 0 32 32" aria-hidden="true" fill="currentColor">
                                    <path d="M7.004 23.087l7.08-7.081-7.07-7.071L8.929 7.02l7.067 7.069L23.084 7l1.912 1.913-7.089 7.093 7.075 7.077-1.912 1.913-7.074-7.073L8.917 25z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
