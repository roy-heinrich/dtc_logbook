@extends('admin.layouts.app')

@php($pageTitle = 'Registered Users')

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Manage registered users</div>
            <div class="text-2xl font-semibold text-slate-900 dark:text-white">User Directory</div>
        </div>
        <a href="{{ route('admin.regusers.trash') }}" title="View Trash" class="self-start inline-flex items-center justify-center p-2 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition lg:self-auto">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </a>
        <form method="GET" class="flex w-full flex-col gap-2 lg:w-auto lg:flex-row lg:items-center">
            <div class="relative w-full lg:w-80">
                <input
                    type="text"
                    id="reguserSearch"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search name, sector, number (min 2 chars)"
                    autocomplete="off"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2 pr-10 text-sm text-slate-700 shadow-sm focus:border-brand-500 focus:ring-brand-500 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200"
                >
                <div id="reguserLoadingSpinner" class="absolute right-3 top-1/2 hidden -translate-y-1/2">
                    <svg class="h-4 w-4 animate-spin text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div
                    id="reguserSuggestions"
                    class="absolute z-20 mt-2 hidden w-full rounded-xl border border-slate-200 bg-white shadow-lg dark:border-slate-800 dark:bg-slate-900"
                >
                    <ul class="divide-y divide-slate-100 text-sm text-slate-700 dark:divide-slate-800 dark:text-slate-200"></ul>
                </div>
            </div>
            <select
                id="reguserSector"
                name="sector"
                class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200 lg:w-56"
            >
                @php($selectedSector = $sector !== '' ? $sector : 'all')
                <option value="all" {{ $selectedSector === 'all' ? 'selected' : '' }}>All sectors</option>
                @forelse ($sectors as $sectorItem)
                    <option value="{{ $sectorItem }}" {{ $selectedSector === $sectorItem ? 'selected' : '' }}>
                        {{ $sectorItem }}
                    </option>
                @empty
                    <option value="all" disabled>No sectors available</option>
                @endforelse
            </select>
            <button
                type="submit"
                class="rounded-xl bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600"
            >
                Search
            </button>
            <a
                href="{{ route('admin.regusers.index') }}"
                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
                Clear Filters
            </a>
        </form>
    </div>

    <div id="regusersTable" class="mt-6">
        @include('admin.regusers._table', ['users' => $users])
    </div>
@endsection

@push('scripts')
<script>
    const reguserSearchInput = document.getElementById('reguserSearch');
    const reguserSectorSelect = document.getElementById('reguserSector');
    const reguserSuggestions = document.getElementById('reguserSuggestions');
    const reguserSuggestionsList = reguserSuggestions?.querySelector('ul');
    const regusersTable = document.getElementById('regusersTable');
    const reguserLoadingSpinner = document.getElementById('reguserLoadingSpinner');

    let reguserDebounceTimer = null;

    function getReguserFilters(page = 1) {
        return {
            q: reguserSearchInput?.value?.trim() || '',
            sector: reguserSectorSelect?.value || 'all',
            page
        };
    }

    function renderSuggestions(items) {
        if (!reguserSuggestionsList) return;
        reguserSuggestionsList.innerHTML = '';

        if (!items || items.length === 0) {
            reguserSuggestions.classList.add('hidden');
            return;
        }

        items.forEach((item) => {
            const li = document.createElement('li');
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'flex w-full flex-col px-4 py-3 text-left hover:bg-slate-50 dark:hover:bg-slate-800/60';
            button.dataset.value = item.label;

            const name = document.createElement('div');
            name.className = 'text-sm font-semibold text-slate-900 dark:text-white';
            name.textContent = item.label;

            const meta = document.createElement('div');
            meta.className = 'text-xs text-slate-500 dark:text-slate-400';
            meta.textContent = [item.sector, item.number].filter(Boolean).join(' · ');

            button.appendChild(name);
            button.appendChild(meta);

            button.addEventListener('click', () => {
                // Extract last name from the formatted label (e.g., "Garcia, Maria Rosario" -> "Garcia")
                const lastName = item.label.split(',')[0].trim();
                reguserSearchInput.value = lastName;
                reguserSuggestions.classList.add('hidden');
                updateRegusersPreview();
            });

            li.appendChild(button);
            reguserSuggestionsList.appendChild(li);
        });

        reguserSuggestions.classList.remove('hidden');
    }

    async function updateRegusersPreview(page = 1) {
        const filters = getReguserFilters(page);

        // Show loading state
        if (reguserLoadingSpinner) {
            reguserLoadingSpinner.classList.remove('hidden');
        }
        if (regusersTable) {
            regusersTable.style.opacity = '0.6';
            regusersTable.style.pointerEvents = 'none';
        }

        try {
            const response = await fetch('{{ route('admin.regusers.preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(filters)
            });

            if (!response.ok) throw new Error('Preview failed');

            const data = await response.json();
            if (regusersTable) {
                regusersTable.innerHTML = data.table || '';
                regusersTable.style.opacity = '1';
                regusersTable.style.pointerEvents = 'auto';
            }

            renderSuggestions(data.suggestions || []);
        } catch (error) {
            console.error('Error loading users:', error);
            if (regusersTable) {
                regusersTable.style.opacity = '1';
                regusersTable.style.pointerEvents = 'auto';
            }
        } finally {
            if (reguserLoadingSpinner) {
                reguserLoadingSpinner.classList.add('hidden');
            }
        }
    }

    function debouncePreview() {
        if (reguserDebounceTimer) {
            clearTimeout(reguserDebounceTimer);
        }
        
        // Only search if there are at least 2 characters or the search is empty
        const searchValue = reguserSearchInput?.value?.trim() || '';
        if (searchValue.length === 1) {
            return; // Don't search on single character
        }
        
        reguserDebounceTimer = setTimeout(() => {
            updateRegusersPreview();
        }, 400);
    }

    function handlePaginationClick(event) {
        const link = event.target.closest('a');
        if (!link || !link.href) return;
        if (!link.href.includes('page=')) return;

        event.preventDefault();
        const url = new URL(link.href);
        const page = parseInt(url.searchParams.get('page') || '1', 10);
        updateRegusersPreview(page);
    }

    document.addEventListener('DOMContentLoaded', () => {
        reguserSearchInput?.addEventListener('input', debouncePreview);
        reguserSectorSelect?.addEventListener('change', () => updateRegusersPreview());
        
        // Prevent form submission on enter key
        reguserSearchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateRegusersPreview();
            }
        });
        
        regusersTable?.addEventListener('click', handlePaginationClick);

        document.addEventListener('click', (event) => {
            if (!reguserSuggestions.contains(event.target) && event.target !== reguserSearchInput) {
                reguserSuggestions.classList.add('hidden');
            }
        });
    });
</script>
@endpush
