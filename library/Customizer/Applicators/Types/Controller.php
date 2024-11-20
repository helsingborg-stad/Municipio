<?php 

namespace Municipio\Customizer\Applicators\Types;

use Municipio\Customizer\Applicators\AbstractApplicator;
use Municipio\Customizer\Applicators\ApplicatorInterface;
use WpService\WpService;

class Controller extends AbstractApplicator implements ApplicatorInterface {
  
  public function __construct(private WpService $wpService){}

  public function getKey(): string
  {
    return 'controller';
  }

  public function applyData(array|object $data)
  {
    $this->wpService->addFilter('Municipio/Controller/Customizer', function($filterInput) use ($data) {
      if(is_array($filterInput)) {
        return array_merge($filterInput, $data);
      }
      return $data;
    });
  }

  public function getData(): object
  {
    //Get field definition
    $fields = $this->getFields();
    $stack = [];

    //Determine what's a controller var, fetch it
    if (is_array($fields) && !empty($fields)) {
        foreach ($fields as $key => $field) {
            // Check if field is a controller
            if (!$this->isFieldType($field, 'controller')) {
                continue;
            }

            if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                $stack = $this->appendStack(
                    $stack,
                    \Kirki::get_option($key),
                    $key,
                    $field
                );
            } else {
                $stack = $this->appendStack(
                    $stack,
                    null,
                    $key,
                    $field
                );
            }
        }
    }
    
    return \Municipio\Helper\FormatObject::camelCase((object) $stack); 
  }

  /**
   * Determine output type
   *
   * @param array $field
   * @return boolean
   */
  private function shouldStackInObject($field, $lookForType = 'controller')
  {
      if (isset($field['output']) && is_array($field['output']) && !empty($field['output'])) {
          foreach ($field['output'] as $output) {
              if (isset($output['type']) && $output['type'] === $lookForType) {
                  if (isset($output['as_object']) && $output['as_object'] === true) {
                      return true;
                  }
              }
          }
      }
      return false;
  }

  /**
   * Append to stack
   *
   * @param array $stack  Prevois stack object
   * @param string $value Value to append
   * @param string $key   Field key to store
   * @param array $field  Field definition
   * @return void
   */
  private function appendStack($stack, $value, $key, $field)
  {
      if ($this->shouldStackInObject($field)) {
          //Get and create object
          $section = $this->sanitizeStackObjectName($field['section']);
          if (!isset($stack[$section]) || !is_array($stack[$section])) {
              $stack[$section] = [];
          }

          //Store in sanitized item name
          $stack[$section][
              $this->sanitizeStackItemName($key, $section)
          ] = $value;
      } else {
          $stack[$key] = $value;
      }

      return $stack;
  }

  /**
   * Sanitize stack object name. Removes customizer panel prefix.
   *
   * @param string $name
   * @return string
   */
  private function sanitizeStackObjectName($name)
  {
      return str_replace('municipio_customizer_panel_', '', $name);
  }

  /**
   * Remove object name from item keys
   *
   * @param string $name
   * @param string $santizationString
   * @return string
   */
  private function sanitizeStackItemName($name, $santizationString)
  {
      return str_replace($santizationString, '', $name);
  }
}