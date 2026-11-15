<x-layouts.public title="Campus Locations - LearnBuddy" description="Manage physical training centres and campus classrooms across Sri Lanka.">
    <section class="min-h-[calc(100vh-140px)] py-8 sm:py-12 px-4 sm:px-6 lg:px-8 bg-neutral-50/60">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-2 text-xs text-neutral-500">
                <a href="{{ route('management.courses.index') }}" class="hover:text-lb-orange">Course Management</a>
                <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
                <span class="text-neutral-800 font-semibold">Campus Locations</span>
            </div>

            <!-- Flash Alert -->
            @if (session('status'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3" role="status">
                    <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-3" role="alert">
                    <i class="fas fa-exclamation-circle text-rose-500 mt-0.5 shrink-0" aria-hidden="true"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-lb-orange text-xs font-semibold uppercase tracking-wide mb-2">
                        <i class="fas fa-map-marker-alt text-xs" aria-hidden="true"></i> Sri Lankan Campuses
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-lb-black tracking-tight">
                        Physical Training Centres
                    </h1>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Configure physical learning centres, classroom capacities, and regional facilities across Sri Lanka.
                    </p>
                </div>

                @can('create', \App\Models\PhysicalLocation::class)
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('management.locations.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-lb-orange text-white hover:bg-orange-600 font-bold text-xs shadow-md transition"
                        >
                            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                            <span>Add New Campus</span>
                        </a>
                    </div>
                @endcan
            </div>

            <!-- Locations Grid / Table -->
            <div class="bg-white rounded-3xl border border-neutral-200/80 shadow-sm overflow-hidden">
                @if ($locations->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider font-semibold border-b border-neutral-200">
                                <tr>
                                    <th class="p-4">Campus Name &amp; Code</th>
                                    <th class="p-4">Province &amp; District</th>
                                    <th class="p-4">Address</th>
                                    <th class="p-4">Room Capacity</th>
                                    <th class="p-4">Batches</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 text-neutral-700">
                                @foreach ($locations as $loc)
                                    <tr class="hover:bg-neutral-50/50">
                                        <td class="p-4">
                                            <div class="font-bold text-neutral-900 text-sm">{{ $loc->name }}</div>
                                            <div class="text-[11px] font-mono text-neutral-400">{{ $loc->code }}</div>
                                            @if ($loc->contact_phone)
                                                <div class="text-[11px] text-neutral-500 mt-0.5">
                                                    <i class="fas fa-phone-alt text-[10px] text-neutral-400 mr-1" aria-hidden="true"></i> {{ $loc->contact_phone }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 font-semibold text-neutral-800">
                                                {{ $loc->city }}
                                            </span>
                                            <div class="text-[11px] text-neutral-400">
                                                {{ $loc->district }}, {{ $loc->province }}
                                            </div>
                                        </td>
                                        <td class="p-4 text-neutral-600 max-w-xs">
                                            <div class="line-clamp-2">{{ $loc->fullAddress() }}</div>
                                        </td>
                                        <td class="p-4 font-bold text-neutral-800 whitespace-nowrap">
                                            {{ $loc->max_room_capacity }} seats
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-neutral-100 font-semibold text-neutral-700">
                                                <i class="fas fa-users text-[10px]" aria-hidden="true"></i>
                                                {{ $loc->batches_count }} {{ Str::plural('batch', $loc->batches_count) }}
                                            </span>
                                        </td>
                                        <td class="p-4 whitespace-nowrap">
                                            @if ($loc->is_active)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-600">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-right whitespace-nowrap space-x-2">
                                            @can('update', $loc)
                                                <a
                                                    href="{{ route('management.locations.edit', $loc) }}"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-xl border border-neutral-300 text-neutral-700 hover:bg-neutral-50 font-semibold"
                                                >
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('delete', $loc)
                                                <button
                                                    type="button"
                                                    data-item="{{ $loc->name }}"
                                                    data-action="{{ route('management.locations.destroy', $loc) }}"
                                                    onclick="confirmDialog({
                                                        title: 'Delete Campus Location',
                                                        itemName: this.dataset.item,
                                                        message: 'Are you sure you want to delete this campus location? Physical batches assigned to this location must be reassigned.',
                                                        action: this.dataset.action,
                                                        method: 'DELETE',
                                                        confirmText: 'Delete Location',
                                                        confirmVariant: 'danger'
                                                    })"
                                                    class="inline-flex items-center px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 font-semibold cursor-pointer"
                                                >
                                                    Delete
                                                </button>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center space-y-3">
                        <i class="fas fa-building text-3xl text-neutral-300 mb-2 block" aria-hidden="true"></i>
                        <h3 class="text-sm font-bold text-neutral-800">No Campus Locations Configured</h3>
                        <p class="text-xs text-neutral-500 max-w-sm mx-auto">
                            Add physical campus centres in Colombo, Kandy, Galle, or other districts to schedule physical learning batches.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layouts.public>
