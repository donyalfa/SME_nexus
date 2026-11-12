<?php

namespace App\Modules\Admin\Services\Intelligence;

use App\Modules\Intelligence\Models\BusinessCategory;
use App\Modules\Intelligence\Repositories\BusinessCategoryRepository;
use App\Modules\Intelligence\Services\AuditLogger;
use DomainException;
    

class BusinessCategoryService
{
    public function __construct(
        private readonly BusinessCategoryRepository $categories,
        private readonly AuditLogger $auditLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters = [])
    {
        return $this->categories->paginate($filters);
    }

    public function all()
    {
        return $this->categories->all();
    }

    public function find(int $id): BusinessCategory
    {
        return $this->categories->find($id);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): BusinessCategory
    {
        $category = $this->categories->create($data);

        $this->auditLogger->log('admin.category.created', 'business_category', $category->getKey(), [], $data);

        return $category;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(BusinessCategory $category, array $data): BusinessCategory
    {
        $oldValues = $category->only(['category_name', 'deskripsi']);

        $category = $this->categories->update($category, $data);

        $this->auditLogger->log('admin.category.updated', 'business_category', $category->getKey(), $oldValues, $data);

        return $category;
    }

    public function delete(BusinessCategory $category): void
    {
        if ($this->categories->hasProfiles($category)) {
            throw new DomainException('Kategori masih digunakan oleh profil UMKM dan tidak dapat dihapus.');
        }

        $oldValues = $category->only(['category_name', 'deskripsi']);
        $categoryId = $category->getKey();

        $this->categories->delete($category);

        $this->auditLogger->log('admin.category.deleted', 'business_category', $categoryId, $oldValues);
    }
}
