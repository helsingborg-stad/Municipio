<?php

namespace Municipio\Controller\Purpose;

/**
 * 'PurposeComponentInterface' defines the standard functionality that
 * every purpose, both simple and complex, should implement.
 *
 * It's the core contract that all purpose types in the project adhere to, ensuring
 * consistency and interoperability among all different types of purposes
 * and it is implemented by the 'PurposeFactory' class that all purposes should inherit.
 */

interface PurposeComponentInterface
{
    /**
     * Gets the label for the purpose component.
     *
     * @return string The label.
     */
    public function getLabel(): string;

    /**
     * Gets the key for the purpose component.
     *
     * @return string The key.
     */
    public function getKey(): string;

    /**
     * Gets the view for the purpose component.
     *
     * @return string The view.
     */
    public function getView(): string;
}
