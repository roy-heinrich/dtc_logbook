<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegUser;
use App\Support\CacheVersion;
use Illuminate\Http\Request;

class RegUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $sector = $request->string('sector')->toString();

        $users = $this->buildUsersQuery($search, $sector)
            ->orderBy('lname_user')
            ->paginate(20)
            ->withQueryString();

        $sectors = RegUser::query()
            ->whereNotNull('sector_user')
            ->where('sector_user', '!=', '')
            ->distinct()
            ->orderBy('sector_user')
            ->pluck('sector_user');

        return view('admin.regusers.index', [
            'users' => $users,
            'search' => $search,
            'sectors' => $sectors,
            'sector' => $sector,
        ]);
    }

    public function trash()
    {
        $users = RegUser::onlyTrashed()
            ->orderBy('lname_user')
            ->paginate(20);

        return view('admin.regusers.trash', [
            'users' => $users,
        ]);
    }

    public function preview(Request $request)
    {
        $search = (string) $request->input('q', '');
        $sector = (string) $request->input('sector', '');
        $page = (int) $request->input('page', 1);

        $query = $this->buildUsersQuery($search, $sector)->orderBy('lname_user');
        
        // Get paginated results for table
        $users = (clone $query)->paginate(20, ['*'], 'page', $page);

        // Only fetch suggestions if actively searching (not empty, not just sector filter)
        $suggestions = collect();
        if ($search !== '' && strlen($search) >= 2) {
            // Reuse the same query results if on first page and have less than 6 results
            if ($page === 1 && $users->count() <= 5) {
                $suggestions = $users->map(function ($user) {
                    return [
                        'id' => $user->user_id,
                        'label' => trim($user->lname_user . ', ' . $user->fname_user . ' ' . ($user->mname_user ?? '')),
                        'sector' => $user->sector_user,
                        'number' => $user->number_user,
                    ];
                })->values();
            } else {
                // Only query separately if needed
                $suggestions = (clone $query)
                    ->limit(5)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->user_id,
                            'label' => trim($user->lname_user . ', ' . $user->fname_user . ' ' . ($user->mname_user ?? '')),
                            'sector' => $user->sector_user,
                            'number' => $user->number_user,
                        ];
                    })
                    ->values();
            }
        }

        return response()->json([
            'table' => view('admin.regusers._table', [
                'users' => $users,
            ])->render(),
            'suggestions' => $suggestions,
        ]);
    }

    public function edit(RegUser $reguser)
    {
        return view('admin.regusers.edit', [
            'user' => $reguser,
        ]);
    }

    public function update(Request $request, RegUser $reguser)
    {
        $data = $request->validate([
            'fname_user' => ['nullable', 'string', 'max:50'],
            'lname_user' => ['required', 'string', 'max:50'],
            'mname_user' => ['nullable', 'string', 'max:50'],
            'suffix_user' => ['nullable', 'string', 'max:2'],
            'birthdate' => ['nullable', 'date'],
            'sex_user' => ['nullable', 'string', 'max:1'],
            'sector_user' => ['nullable', 'string', 'max:100'],
            'number_user' => ['nullable', 'string', 'max:15'],
        ]);

        $reguser->update($data);
        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.regusers.edit', $reguser)
            ->with('status', 'User updated successfully.');
    }

    public function destroy(RegUser $reguser)
    {
        $reguser->delete();
        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.regusers.index')
            ->with('status', 'User deleted successfully.');
    }

    public function restore(string $reguserId)
    {
        $user = RegUser::onlyTrashed()->findOrFail($reguserId);
        $user->restore();
        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.regusers.trash')
            ->with('status', 'User restored successfully.');
    }

    public function forceDelete(string $reguserId)
    {
        $user = RegUser::onlyTrashed()->findOrFail($reguserId);
        $user->forceDelete();
        CacheVersion::bumpMany(['dashboard', 'activities_filters', 'login_logs_filters', 'reports']);

        return redirect()
            ->route('admin.regusers.trash')
            ->with('status', 'User permanently deleted.');
    }

    private function buildUsersQuery(string $search, string $sector)
    {
        return RegUser::query()
            ->select(['user_id', 'fname_user', 'lname_user', 'mname_user', 'suffix_user', 'sector_user', 'number_user', 'birthdate', 'sex_user'])
            ->when($search !== '', function ($query) use ($search) {
                // Handle comma-separated searches (e.g., "Garcia, Maria")
                $parts = array_map('trim', explode(',', $search));
                
                $query->where(function ($inner) use ($search, $parts) {
                    // Search in individual columns
                    $searchLower = strtolower($search);
                    $inner->whereRaw('LOWER(fname_user) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(lname_user) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(mname_user) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(sector_user) LIKE ?', ["%{$searchLower}%"])
                        ->orWhereRaw('LOWER(number_user) LIKE ?', ["%{$searchLower}%"]);
                    
                    // If comma-separated, also search for combinations
                    if (count($parts) >= 2) {
                        $part1Lower = strtolower($parts[0]);
                        $part2Lower = strtolower($parts[1]);
                        
                        // Search for "Last, First" or "First, Last"
                        $inner->orWhereRaw('LOWER(lname_user) LIKE ? AND LOWER(fname_user) LIKE ?', ["%{$part1Lower}%", "%{$part2Lower}%"])
                            ->orWhereRaw('LOWER(fname_user) LIKE ? AND LOWER(lname_user) LIKE ?', ["%{$part1Lower}%", "%{$part2Lower}%"]);
                    }
                });
            })
            ->when($sector !== '' && $sector !== 'all', function ($query) use ($sector) {
                $query->where('sector_user', $sector);
            });
    }
}
