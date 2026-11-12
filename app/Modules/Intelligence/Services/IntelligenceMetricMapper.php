<?php

namespace App\Modules\Intelligence\Services;

class IntelligenceMetricMapper
{
    /**
     * Map input Autopsy menjadi metrik untuk Rule Engine.
     *
     * @param array<string, mixed> $data
     * @return array<string, float>
     */
    public function fromAutopsyInput(array $data): array
    {
        $omzetPrev = (float) ($data['omzet_prev'] ?? 0);
        $omzetCurrent = (float) ($data['omzet_current'] ?? 0);

        $customerPrev = (float) ($data['customer_prev'] ?? 0);
        $customerCurrent = (float) ($data['customer_current'] ?? 0);

        $salesPrev = (float) ($data['sales_prev'] ?? 0);
        $salesCurrent = (float) ($data['sales_current'] ?? 0);

        $marketingSpend = (float) ($data['marketing_spend'] ?? 0);
        $operationalCost = (float) ($data['operational_cost'] ?? 0);

        $revenueChange = $omzetPrev > 0
            ? (($omzetCurrent - $omzetPrev) / $omzetPrev) * 100
            : 0.0;

        $customerChange = $customerPrev > 0
            ? (($customerCurrent - $customerPrev) / $customerPrev) * 100
            : 0.0;

        $salesChange = $salesPrev > 0
            ? (($salesCurrent - $salesPrev) / $salesPrev) * 100
            : 0.0;

        $operatingExpenseRatio = $omzetCurrent > 0
            ? ($operationalCost / $omzetCurrent) * 100
            : 0.0;

        $profitMargin = $omzetCurrent > 0
            ? (($omzetCurrent - $operationalCost - $marketingSpend) / $omzetCurrent) * 100
            : 0.0;

        return [
            'revenue_change_percent' => $revenueChange,
            'customer_change_percent' => $customerChange,
            'sales_change_percent' => $salesChange,
            'operating_expense_ratio' => $operatingExpenseRatio,
            'profit_margin' => $profitMargin,
        ];
    }
}
