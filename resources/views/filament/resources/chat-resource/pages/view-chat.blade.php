<x-filament::page>
    <div class="space-y-4">
        <!-- Chat Header -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Chat between {{ $record->user->name }} (User ID: {{ $record->user->id }}) and {{ $record->owner->name }} (Owner ID: {{ $record->owner->id }})
            </h2>
        </div>

        <!-- Chat Messages -->
        <div class="space-y-4">
            @foreach ($messages as $message)
                <div class="flex {{ $message->sender_type === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-md p-4 rounded-lg shadow
                        {{ $message->sender_type === 'user' ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-100 dark:bg-gray-700' }}">
                        <!-- Sender Information -->
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                            @if ($message->sender_type === 'user')
                                {{ $record->user->name }} (User ID: {{ $record->user->id }})
                            @else
                                {{ $record->owner->name }} (Owner ID: {{ $record->owner->id }})
                            @endif
                        </p>

                        <!-- Message Content -->
                        <p class="text-sm text-gray-700 dark:text-gray-200 mt-2">
                            {{ $message->message }}
                        </p>

                        <!-- Timestamp -->
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $message->created_at->format('h:i A, M d') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::page>