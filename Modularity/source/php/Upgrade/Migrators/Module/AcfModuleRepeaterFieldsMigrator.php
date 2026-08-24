<?php

declare(strict_types=1);

namespace Modularity\Upgrade\Migrators\Module;

use Modularity\Upgrade\Migrators\MigratorInterface;
use WP_CLI;

/**
 * @mago-expect lint:cyclomatic-complexity
 * @mago-expect lint:kan-defect
 */
class AcfModuleRepeaterFieldsMigrator implements MigratorInterface
{
    private $newField;
    private $oldFieldValue;
    private $moduleId;

    public function __construct($newField, $oldFieldValue, $moduleId)
    {
        $this->newField = $newField;
        $this->oldFieldValue = $oldFieldValue;
        $this->moduleId = $moduleId;
    }

    public function migrate(): mixed
    {
        $subFields = $this->newField['fields'];

        if (!is_array($subFields) || $subFields === [] || !is_array($this->oldFieldValue)) {
            return false;
        }

        if ($this->oldFieldValue === []) {
            return true;
        }

        $fieldWasUpdated = (bool) update_field(
            $this->newField['name'],
            $this->oldFieldValue,
            $this->moduleId,
        );

        return $this->migrateRepeaterSubFields($subFields, $fieldWasUpdated);
    }

    private function migrateRepeaterSubFields(array $subFields, bool $fieldWasUpdated): bool
    {
        foreach ($this->oldFieldValue as $rowIndex => $oldRow) {
            if (!is_array($oldRow)) {
                continue;
            }

            foreach ($subFields as $oldFieldName => $newFieldName) {
                if (!array_key_exists($oldFieldName, $oldRow)) {
                    continue;
                }

                $fieldWasUpdated =
                    (bool) update_sub_field(
                        [$this->newField['name'], $rowIndex + 1, $newFieldName],
                        $oldRow[$oldFieldName],
                        $this->moduleId,
                    ) || $fieldWasUpdated;
            }
        }

        $deviations = $this->findDeviations($subFields);
        if ($deviations === [] && $fieldWasUpdated) {
            WP_CLI::line(sprintf(
                'Updating repeater field %s with sub fields in %s',
                (string) $this->newField['name'],
                (string) $this->moduleId,
            ));
        }

        foreach ($deviations as $deviation) {
            WP_CLI::warning(sprintf(
                'Failed to verify repeater field %s in module %s, row %d, source sub field %s, target sub field %s',
                (string) $this->newField['name'],
                (string) $this->moduleId,
                $deviation['row'],
                $deviation['source'],
                $deviation['target'],
            ));
        }

        return $deviations === [];
    }

    /**
     * ACF returns false both for failed writes and unchanged values, so migration success must be
     * based on the formatted value that readers will receive after the write.
     */
    private function findDeviations(array $subFields): array
    {
        $migratedRows = get_field($this->newField['name'], $this->moduleId);
        $migratedRows = is_array($migratedRows) ? $migratedRows : [];
        $deviations = [];

        foreach ($this->oldFieldValue as $rowIndex => $oldRow) {
            foreach ($subFields as $oldFieldName => $newFieldName) {
                if (!is_array($oldRow) || !array_key_exists($oldFieldName, $oldRow)) {
                    continue;
                }

                $newRow = $migratedRows[$rowIndex] ?? null;
                if (!is_array($newRow) || !array_key_exists($newFieldName, $newRow) || !$this->valuesMatch($oldRow[$oldFieldName], $newRow[$newFieldName])) {
                    $deviations[] = [
                        'row' => $rowIndex + 1,
                        'source' => (string) $oldFieldName,
                        'target' => (string) $newFieldName,
                    ];
                }
            }
        }

        return $deviations;
    }

    /**
     * Compare source data with ACF's formatted read value while accounting for common ACF return
     * formats without collapsing distinct falsy values.
     */
    private function valuesMatch(mixed $sourceValue, mixed $targetValue): bool
    {
        if ($sourceValue === $targetValue) {
            return true;
        }

        if (is_numeric($sourceValue) && is_numeric($targetValue)) {
            return (float) $sourceValue === (float) $targetValue;
        }

        if (is_string($sourceValue) && is_array($targetValue) && array_key_exists('url', $targetValue)) {
            return $sourceValue === $targetValue['url'];
        }

        if (is_array($sourceValue) && array_key_exists('url', $sourceValue) && is_string($targetValue)) {
            return $sourceValue['url'] === $targetValue;
        }

        if (is_array($sourceValue) && array_key_exists('ID', $sourceValue) && is_numeric($targetValue)) {
            return is_numeric($sourceValue['ID']) && (float) $sourceValue['ID'] === (float) $targetValue;
        }

        if (is_numeric($sourceValue) && is_array($targetValue) && array_key_exists('ID', $targetValue)) {
            return is_numeric($targetValue['ID']) && (float) $sourceValue === (float) $targetValue['ID'];
        }

        if (!is_array($sourceValue) || !is_array($targetValue) || array_keys($sourceValue) !== array_keys($targetValue)) {
            return false;
        }

        foreach ($sourceValue as $key => $value) {
            if (!$this->valuesMatch($value, $targetValue[$key])) {
                return false;
            }
        }

        return true;
    }
}
