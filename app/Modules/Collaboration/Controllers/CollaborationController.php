<?php

namespace App\Modules\Collaboration\Controllers;

use App\Modules\Collaboration\Models\Company;
use App\Modules\Collaboration\Models\MatchModel;
use App\Modules\Collaboration\Models\Swipe;
use App\Modules\Collaboration\Services\CollaborationProfileSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CollaborationController
{
    public function __construct(
        private CollaborationProfileSyncService $profileSync
    ) {
    }

    /**
     * Menampilkan rekomendasi UMKM untuk pengguna yang login.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        $currentCompany = $this->profileSync->syncForUser($user);

        if (!$currentCompany) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Lengkapi profil UMKM terlebih dahulu untuk menggunakan Nexus Radar.');
        }

        $recommendation = Company::with('businessProfile.category')
            ->where('companies.id', '!=', $currentCompany->id)
            ->whereNotNull('companies.id_business_profile')
            ->whereDoesntHave('swipesAsSwiped', function ($query) use ($currentCompany) {
                $query->where(
                    'swiper_company_id',
                    $currentCompany->id
                );
            })
            ->where(function ($query) use ($user) {
                $query->whereNull('companies.user_id')
                    ->orWhere('companies.user_id', '!=', $user->id_user);
            })
            ->first();

        return view('collaboration.index', compact(
            'recommendation',
            'currentCompany'
        ));
    }

    /**
     * Memproses aksi Pass atau Connect.
     */
    public function swipe(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'swiped_id' => ['required', 'integer', 'exists:companies,id'],
            'is_interested' => ['required', 'boolean'],
        ]);

        $user = Auth::user();
        $currentCompany = $this->profileSync->syncForUser($user);

        if (!$currentCompany) {
            return redirect()
                ->route('collaboration.index')
                ->with('error', 'Profil UMKM belum tersedia.');
        }

        $swipedId = (int) $validated['swiped_id'];
        $isInterested = (bool) $validated['is_interested'];

        if ($swipedId === $currentCompany->id) {
            return back()->with('error', 'Anda tidak dapat melakukan swipe pada profil sendiri.');
        }

        $targetCompany = Company::whereKey($swipedId)
            ->whereNotNull('id_business_profile')
            ->where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', '!=', $user->id_user);
            })
            ->first();

        if (!$targetCompany) {
            return back()->with('error', 'Profil UMKM tidak ditemukan.');
        }

        $alreadySwiped = Swipe::where('swiper_company_id', $currentCompany->id)
            ->where('swiped_company_id', $swipedId)
            ->exists();

        if ($alreadySwiped) {
            return redirect()->route('collaboration.index');
        }

        $mutualMatch = DB::transaction(function () use (
            $currentCompany,
            $swipedId,
            $isInterested
        ) {
            Swipe::create([
                'swiper_company_id' => $currentCompany->id,
                'swiped_company_id' => $swipedId,
                'direction' => $isInterested ? 'like' : 'pass',
            ]);

            if (!$isInterested) {
                return false;
            }

            $mutualLike = Swipe::where('swiper_company_id', $swipedId)
                ->where('swiped_company_id', $currentCompany->id)
                ->where('direction', 'like')
                ->exists();

            if (!$mutualLike) {
                return false;
            }

            $companyA = min($currentCompany->id, $swipedId);
            $companyB = max($currentCompany->id, $swipedId);

            MatchModel::firstOrCreate(
                [
                    'company_a_id' => $companyA,
                    'company_b_id' => $companyB,
                ],
                [
                    'matched_at' => now(),
                ]
            );

            return true;
        });

        if ($mutualMatch) {
            return redirect()
                ->route('collaboration.index')
                ->with('match_success', 'NEXUS ESTABLISHED! Anda memiliki kecocokan baru.');
        }

        return redirect()->route('collaboration.index');
    }
}
