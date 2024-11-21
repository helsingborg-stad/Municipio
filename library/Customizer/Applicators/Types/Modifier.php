<?php 

namespace Municipio\Customizer\Applicators\Types;

use Municipio\Customizer\Applicators\AbstractApplicator;
use Municipio\Customizer\Applicators\ApplicatorInterface;
use WpService\WpService;

class Modifier extends AbstractApplicator implements ApplicatorInterface {

  public function __construct(private WpService $wpService){}

  public function getKey(): string
  {
    return 'modifier';
  }

  public function applyData(array|object $data)
  {
    $this->wpService->addFilter('ComponentLibrary/Component/Modifier', [$this, 'applyDataFilterFunction'], 10, 2);
  }

  /**
   * Apply data to filter 
   * 
   * @param array $modifiers
   * @param array $contexts
   * 
   * @return array
   */
  public function applyDataFilterFunction(array|string $modifiers, array|string $contexts): array
  {
    $contexts = is_array($contexts) ? $contexts : [$contexts];
    $modifiers = is_array($modifiers) ? $modifiers : [$modifiers];

    foreach ($modifiers as $filter) {

        $passFilterRules = false;

        foreach ($filter['contexts'] as $filterContext) {
            // Operator and context must be set
            if (!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                throw new \Error("Operator must be != or == to be used in ComponentData applicator. Context must be set. Provided values: " . print_r($filterContext, true));
            }

            // Operator must be != or ==
            if (!in_array($filterContext['operator'], ["!=", "=="])) {
                throw new \Error("Operator must be != or == to be used in ComponentData applicator. Provided value: " . $filterContext['operator']);
            }

            if (
                ($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))
            ) {
                $passFilterRules = true;
            }
        }

        if ($passFilterRules) {
            $modifiers[] = $filter['value'];
        }
    }

    return $modifiers;
  }

  /**
   * Get data
   * 
   * @return array
   */
  public function getData(): array
  {
    $fields    = $this->getFields();

    if (is_array($fields) && !empty($fields)) {
        foreach ($fields as $key => $field) {
            if (!$this->isFieldType($field, 'modifier')) {
                continue;
            }

            if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                if (!isset($field['output']) || !is_array($field['output'])) {
                    continue;
                }

                foreach ($field['output'] as $output) {
                    if (!isset($output['context']) || !is_array($output['context'])) {
                        continue;
                    }

                    foreach ($output['context'] as $contextKey => $context) {
                        if (!is_array($context)) {
                            $output['context'][$contextKey] = [
                                'operator' => '==',
                                'context'  => $context
                            ];
                        }
                    }

                    $modifiers[] = [
                        'contexts' => $output['context'],
                        'value'    => \Kirki::get_option($key),
                    ];
                }
            }
        }
    }

    return $modifiers;
  }
}