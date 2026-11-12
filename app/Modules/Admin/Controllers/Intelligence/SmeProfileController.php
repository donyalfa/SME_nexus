<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreSmeProfileRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateSmeProfileRequest;
use App\Modules\Intelligence\Models\SmeProfile;
use App\Modules\Admin\Services\Intelligence\BusinessCategoryService;
use App\Modules\Admin\Services\Intelligence\SmeProfileService;
use App\Modules\Admin\Services\Intelligence\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmeProfileController extends Controller
{
    public function __construct(
        private readonly SmeProfileService $profiles,
        private readonly BusinessCategoryService $categories,
        private readonly UserService $users,
    ) {}

    public function index(Request $request): View
    {
        return view('admin.intelligence.profiles.index', [
            'profiles' => $this->profiles->paginate(
                $request->only(['search', 'id_category', 'id_user'])
            ),
            'filters' => $request->only(['search', 'id_category', 'id_user']),
            'categories' => $this->categories->all(),
            'owners' => $this->users->owners(),
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.profiles.form', [
            'profile' => new SmeProfile(['established_year' => date('Y')]),
            'categories' => $this->categories->all(),
            'owners' => $this->users->ownersForSelection(),
        ]);
    }

    public function store(StoreSmeProfileRequest $request): RedirectResponse
    {
        $this->profiles->create($request->validated());

        return redirect()
            ->route('admin.intelligence.profiles.index')
            ->with('success', 'Profil UMKM berhasil ditambahkan.');
    }

    public function edit(SmeProfile $profile): View
    {
        return view('admin.intelligence.profiles.form', [
            'profile' => $profile,
            'categories' => $this->categories->all(),
            'owners' => $this->users->ownersForSelection($profile->id_user),
        ]);
    }

    public function update(
        UpdateSmeProfileRequest $request,
        SmeProfile $profile
    ): RedirectResponse {
        $this->profiles->update($profile, $request->validated());

        return redirect()
            ->route('admin.intelligence.profiles.index')
            ->with('success', 'Profil UMKM berhasil diperbarui.');
    }

    public function destroy(SmeProfile $profile): RedirectResponse
    {
        $this->profiles->delete($profile);

        return redirect()
            ->route('admin.intelligence.profiles.index')
            ->with('success', 'Profil UMKM dan seluruh data terkait berhasil dihapus permanen.');
    }
}
