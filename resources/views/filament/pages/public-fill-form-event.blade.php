<div style="display:flex; justify-content:center; align-items: center; min-height: 100vh;margin-top:20px" class="w-full max-w-4xl mx-auto py-4 fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div style="min-height: 100vh;max-width:100%;width:100%" class="flex flex-col items-center justify-center p-4 w-full">
        @if($url->filled)
            <div class="text-center">
                <svg class="mx-auto h-16 w-16" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="rgb(34,197,94,1)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <h1 class="mt-4 text-3xl font-bold text-gray-800">{{__("Thank you")}}!</h1>
                <p class="mt-2 text-gray-600">
                {{__("Your data has been sent successfully. Thank you for your participation")}}!
                </p>
            </div>
        @else 
            <h1 class="mb-4 text-center text-2xl font-bold w-full">{{__("Fill the form bellow with the your data")}}</h1>
            <form wire:submit.prevent="submit" class="w-full">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" class="w-full">
                        {{__("Send data")}}
                    </x-filament::button>
                </div>
            </form>
        @endif
    </div>
</div>