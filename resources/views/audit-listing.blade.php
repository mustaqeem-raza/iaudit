<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Listing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Search and Filters -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('audit.index') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <!-- Search -->
                                <div>
                                    <label for="search" class="block font-medium text-sm text-gray-700">Search</label>
                                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="search" name="search"
                                           value="{{ request('search') }}" placeholder="Reference, user, ship...">
                                </div>

                                <!-- Status Filter -->
                                <div>
                                    <label for="status" class="block font-medium text-sm text-gray-700">Status</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="status" name="status">
                                        <option value="">All</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    </select>
                                </div>

                                <!-- User Filter -->
                                <div>
                                    <label for="user_id" class="block font-medium text-sm text-gray-700">User</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="user_id" name="user_id">
                                        <option value="">All</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Ship Filter -->
                                <div>
                                    <label for="ship_id" class="block font-medium text-sm text-gray-700">Ship</label>
                                    <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="ship_id" name="ship_id">
                                        <option value="">All</option>
                                        @foreach($ships as $ship)
                                            <option value="{{ $ship->id }}" {{ request('ship_id') == $ship->id ? 'selected' : '' }}>
                                                {{ $ship->name }} ({{ $ship->mnemonic }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <!-- Date From -->
                                <div>
                                    <label for="date_from" class="block font-medium text-sm text-gray-700">From Date</label>
                                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="date_from" name="date_from"
                                           value="{{ request('date_from') }}">
                                </div>

                                <!-- Date To -->
                                <div>
                                    <label for="date_to" class="block font-medium text-sm text-gray-700">To Date</label>
                                    <input type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" id="date_to" name="date_to"
                                           value="{{ request('date_to') }}">
                                </div>

                                <!-- Buttons -->
                                <div class="flex items-end gap-2">
                                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Filter
                                    </button>
                                    <a href="{{ route('audit.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Audit Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ship</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($audits as $audit)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $audit->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $audit->reference_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($audit->user)
                                                {{ $audit->user->first_name }} {{ $audit->user->last_name }}
                                                <br>
                                                <small class="text-gray-500">{{ $audit->user->email }}</small>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($audit->ship)
                                                {{ $audit->ship->name }} ({{ $audit->ship->mnemonic }})
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $audit->status == 'completed' ? 'green' : 'yellow' }}-100 text-{{ $audit->status == 'completed' ? 'green' : 'yellow' }}-800">
                                                {{ ucfirst($audit->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $audit->score ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $audit->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="action-menu" style="position: relative; display: inline-block;">
                                                <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-1 px-2 rounded" onclick="toggleActionMenu({{ $audit->id }})">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                    </svg>
                                                </button>
                                                <div class="action-dropdown" id="action-menu-{{ $audit->id }}" style="display: none; position: absolute; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); z-index: 1000; min-width: 160px;">
                                                    <a href="{{ route('audit.report.show', $audit->id) }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #333;">Plain Report</a>
                                                    <a href="{{ route('audit.report.pdf.show', $audit->id) }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #333;">PDF Report</a>
                                                    <a href="{{ route('audit.download', $audit->id) }}" style="display: block; padding: 8px 16px; text-decoration: none; color: #333;">Download Report</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No audits found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $audits->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleActionMenu(id) {
            var menu = document.getElementById('action-menu-' + id);
            var isVisible = menu.style.display === 'block';
            // Close all menus first
            var dropdowns = document.querySelectorAll('.action-dropdown');
            for (var i = 0; i < dropdowns.length; i++) {
                dropdowns[i].style.display = 'none';
            }
            // Toggle current menu
            menu.style.display = isVisible ? 'none' : 'block';

            // Close when clicking outside
            if (!isVisible) {
                setTimeout(function() {
                    document.addEventListener('click', function closeMenu(e) {
                        if (!menu.contains(e.target) && !e.target.closest('.action-menu button')) {
                            menu.style.display = 'none';
                            document.removeEventListener('click', closeMenu);
                        }
                    });
                }, 0);
            }
        }
    </script>
</x-app-layout>
