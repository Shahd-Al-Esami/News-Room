<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;
class NotOffensive implements ValidationRule
{
   
  /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */

    // Add your blacklist words or integrate an API/service
    protected array $blacklist = ['badword1', 'badword2', 'spam'];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $content = strtolower($value);
        foreach ($this->blacklist as $word) {
            if (str_contains($content, $word)) {
                $fail("The {$attribute} contains inappropriate content.");
                return;
            }
        }
    }
}
