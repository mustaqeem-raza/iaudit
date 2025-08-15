<style>
.text-gray-900
 {
    display: flex;
    justify-content: space-between;
}
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    <a href="{{ route('audit.report') }}" target="_blank" 
                        style="margin-left:15px; padding:6px 12px; background:#007bff; color:white; text-decoration:none; border-radius:4px;">
                        Preview Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
