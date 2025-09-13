<div>
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
        <h1 class="mb-4 text-center text-2xl font-bold">{{__("Fill the form bellow with the your data")}}</h1>
        <form wire:submit.prevent="submit">
            {{ $this->form }}
            <div class="mt-4">
                <x-filament::button type="submit" class="w-full">
                    {{__("Send data")}}
                </x-filament::button>
            </div>
        </form>
    @endif
</div>