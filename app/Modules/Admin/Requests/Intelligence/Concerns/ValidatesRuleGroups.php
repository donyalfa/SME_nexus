<?php

namespace App\Modules\Admin\Requests\Intelligence\Concerns;

use Illuminate\Contracts\Validation\Validator;

trait ValidatesRuleGroups
{
    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $groups = $this->input('groups', []);

                if (! is_array($groups)) {
                    return;
                }

                foreach ($groups as $groupIndex => $group) {
                    if (! is_array($group) || ! is_array($group['conditions'] ?? null)) {
                        continue;
                    }

                    $symptomIds = array_filter(array_map(
                        fn ($condition) => is_array($condition) ? ($condition['id_symptom'] ?? null) : null,
                        $group['conditions']
                    ));

                    if (count($symptomIds) !== count(array_unique($symptomIds))) {
                        $validator->errors()->add(
                            "groups.{$groupIndex}.conditions",
                            'Satu gejala tidak boleh dipilih lebih dari sekali dalam grup yang sama.'
                        );
                    }
                }
            },
        ];
    }
}
