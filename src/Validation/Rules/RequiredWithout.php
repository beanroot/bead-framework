<?php

/**
 * @author Darren Edale
 * @version 0.9.2
 * @date May 2022
 */

declare(strict_types=1);

namespace Equit\Validation\Rules;

use Equit\Validation\DatasetAwareRule;
use InvalidArgumentException;
use function Equit\Traversable\all;

/**
 * Validator rule to ensure that some data is non-empty if at least one of another set of fields is empty.
 */
class RequiredWithout implements DatasetAwareRule
{
    use KnowsDataset;
    use ReferencesOtherFields;
    use ChecksDataForEmptiness;

    /**
     * Initialise a new instance of the rule.
     *
     * @param array<string> $otherFields The other fields to check with.
     */
    public function __construct(array $otherFields)
    {
        assert (!empty($otherFields), (8 <= PHP_MAJOR_VERSION
            ? new InvalidArgumentException("Argument for parameter \$otherFields must not be an empty array.")
            : "Argument for parameter \$otherFields must not be an empty array."
        ));
        $this->setOtherFields($otherFields);
    }

    /**
     * Helper to check whether all the related fields are non-empty.
     *
     * @return bool `true` if all the fields are present and non-empty, `false` otherwise.
     */
    protected function otherFieldsArePresent(): bool
    {
        $data = $this->dataset();
        return all($this->otherFields(), fn(string $field): bool => self::isFilled($data[$field] ?? null));
    }

    /**
     * Check some data against the rule.
     *
     * The data passes the rule if all the related fields are non-empty or if the data tested is not empty.
     *
     * @param string $field The field name of the data being checked.
     * @param mixed $data The data to check.
     *
     * @return bool `true` if the data passes, `false` otherwise.
     */
    public function passes(string $field, $data): bool
    {
        return self::isFilled($data) || $this->otherFieldsArePresent();
    }

    /**
     * Fetch the default message for when the data does not pass the rule.
     *
     * @param string $field The field under validation.
     *
     * @return string The message.
     */
    public function message(string $field): string
    {
        if (1 == count($this->otherFields())) {
            return tr("The %1 field must not be empty if the %2 field is not filled.", __FILE__, __LINE__, $field, $this->otherFields()[0]);
        }

        return tr(
            "The %1 field must not be empty if one or more of the the %2 fields is not filled.",
            __FILE__,
            __LINE__,
            $field,
            grammaticalImplode($this->otherFields(), ", ", tr(" and "))
        );
    }
}
