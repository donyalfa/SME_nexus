<?php

namespace App\Modules\Admin\Controllers\Intelligence;

use App\Shared\Http\Controllers\Controller;
use App\Modules\Admin\Requests\Intelligence\StoreBusinessCategoryRequest;
use App\Modules\Admin\Requests\Intelligence\UpdateBusinessCategoryRequest;
use App\Modules\Intelligence\Models\BusinessCategory;
use App\Modules\Admin\Services\Intelligence\BusinessCategoryService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessCategoryController extends Controller
{
    public function __construct(private readonly BusinessCategoryService $categories) {}

    public function index(Request $request): View
    {
        return view('admin.intelligence.categories.index', [
            'categories' => $this->categories->paginate($request->only('search')),
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): View
    {
        return view('admin.intelligence.categories.form', [
            'category' => new BusinessCategory(),
        ]);
    }

    public function store(StoreBusinessCategoryRequest $request): RedirectResponse
    {
        $this->categories->create($request->validated());

        return redirect()
            ->route('admin.intelligence.categories.index')
            ->with('success', 'Kategori bisnis berhasil ditambahkan.');
    }

    public function edit(BusinessCategory $category): View
    {
        return view('admin.intelligence.categories.form', [
            'category' => $category,
        ]);
    }

    public function update(UpdateBusinessCategoryRequest $request, BusinessCategory $category): RedirectResponse
    {
        $this->categories->update($category, $request->validated());

        return redirect()
            ->route('admin.intelligence.categories.index')
            ->with('success', 'Kategori bisnis berhasil diperbarui.');
    }

    public function destroy(BusinessCategory $category): RedirectResponse
    {
        try {
            $this->categories->delete($category);

            return redirect()
                ->route('admin.intelligence.categories.index')
                ->with('success', 'Kategori bisnis berhasil dihapus.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage());
        }
    }
}
