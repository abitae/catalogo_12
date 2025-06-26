@props(['type' => 'info', 'message' => '', 'show' => false])

@if($show)
<div x-data="{ show: true }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     @class([
         'fixed top-4 right-4 z-50 max-w-sm w-full',
         'bg-white dark:bg-zinc-800 border rounded-lg shadow-lg p-4',
         'border-green-200 dark:border-green-700' => $type === 'success',
         'border-red-200 dark:border-red-700' => $type === 'error',
         'border-yellow-200 dark:border-yellow-700' => $type === 'warning',
         'border-blue-200 dark:border-blue-700' => $type === 'info',
     ])>
    <div class="flex items-start">
        <div class="flex-shrink-0">
            @if($type === 'success')
                <flux:icon name="check-circle" class="w-6 h-6 text-green-500" />
            @elseif($type === 'error')
                <flux:icon name="x-circle" class="w-6 h-6 text-red-500" />
            @elseif($type === 'warning')
                <flux:icon name="exclamation-triangle" class="w-6 h-6 text-yellow-500" />
            @else
                <flux:icon name="information-circle" class="w-6 h-6 text-blue-500" />
            @endif
        </div>
        <div class="ml-3 flex-1">
            <p @class([
                'text-sm font-medium',
                'text-green-800 dark:text-green-200' => $type === 'success',
                'text-red-800 dark:text-red-200' => $type === 'error',
                'text-yellow-800 dark:text-yellow-200' => $type === 'warning',
                'text-blue-800 dark:text-blue-200' => $type === 'info',
            ])>
                {{ $message }}
            </p>
        </div>
        <div class="ml-4 flex-shrink-0">
            <button @click="show = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                <flux:icon name="x" class="w-5 h-5" />
            </button>
        </div>
    </div>
</div>
@endif
