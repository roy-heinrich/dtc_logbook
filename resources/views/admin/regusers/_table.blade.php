<div class="rounded-2xl glass-card shadow-sm overflow-hidden">
    <!-- Mobile/Tablet View - Card Layout -->
    <div class="md:hidden space-y-3 p-4">
        @forelse ($users as $user)
            @php($displaySuffix = filled($user->suffix_user) && !preg_match('/^N\s*\/?\s*A?$/i', trim($user->suffix_user)) ? trim($user->suffix_user) : null)
            @php($nameExtras = trim(implode(' ', array_filter([$user->mname_user, $displaySuffix]))))
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800">
                <div class="space-y-2">
                    <div class="flex justify-between items-start gap-2">
                        <span class="font-semibold text-slate-600 dark:text-slate-300 text-sm">Name</span>
                        <div class="text-right">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $user->lname_user }}, {{ $user->fname_user }}</div>
                            @if($nameExtras !== '')
                                <span class="text-xs text-slate-500">{{ $nameExtras }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-2 text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Birth Date:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->birthdate?->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-2 text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Sex:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->sex_user }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-2 text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Sector:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->sector_user }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-2 text-sm">
                        <span class="text-slate-600 dark:text-slate-400">Phone:</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $user->number_user }}</span>
                    </div>
                    <div class="flex justify-end pt-2 border-t border-slate-200 dark:border-slate-700">
                        <a
                            href="{{ route('admin.regusers.edit', $user) }}"
                            class="rounded-lg border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40"
                        >
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                No users found.
            </div>
        @endforelse
    </div>

    <!-- Desktop View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-widest text-slate-400 dark:bg-slate-950">
                <tr>
                    <th class="px-4 py-3 rounded-tl-xl">Last Name</th>
                    <th class="px-4 py-3">First Name</th>
                    <th class="px-4 py-3">Middle Name</th>
                    <th class="px-4 py-3">Suffix</th>
                    <th class="px-4 py-3">Birth Date</th>
                    <th class="px-4 py-3">Sex</th>
                    <th class="px-4 py-3">Sector</th>
                    <th class="px-4 py-3">Phone Number</th>
                    <th class="px-4 py-3 rounded-tr-xl"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($users as $user)
                    @php($displaySuffix = filled($user->suffix_user) && !preg_match('/^N\s*\/?\s*A?$/i', trim($user->suffix_user)) ? trim($user->suffix_user) : null)
                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-950">
                        <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white {{ $loop->last ? 'rounded-bl-xl' : '' }}">{{ $user->lname_user }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->fname_user }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->mname_user }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $displaySuffix }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->birthdate?->format('Y-m-d') }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->sex_user }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->sector_user }}</td>
                        <td class="px-4 py-4 text-slate-600 dark:text-slate-300">{{ $user->number_user }}</td>
                        <td class="px-4 py-4 text-right {{ $loop->last ? 'rounded-br-xl' : '' }}">
                            <a
                                href="{{ route('admin.regusers.edit', $user) }}"
                                class="rounded-lg border border-brand-200 px-3 py-1 text-xs font-semibold text-brand-700 hover:bg-brand-50 dark:border-brand-700 dark:text-brand-200 dark:hover:bg-brand-900/40"
                            >
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-6 text-center text-sm text-slate-500 dark:text-slate-400">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
