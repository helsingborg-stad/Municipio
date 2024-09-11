<?php

namespace Municipio\ImageConvert\Contract;

interface ImageContractInterface
{
    /**
     * Get the ID.
     *
     * @return int The ID of the contract.
     */
    public function getId(): int;

    /**
     * Get the URL.
     *
     * @return string The URL associated with the contract.
     */
    public function getUrl(): string;

    /**
     * Set the URL.
     *
     * @param string $url The URL value.
     */
    public function setUrl(string $url): void;

    /**
     * Get path
     *
     * @return string The path associated with the contract.
     */
    public function getPath(): string;

    /**
     * Set path
     *
     * @param string $path The path value.
     */
    public function setPath(string $path): void;

    /**
     * Get width, height array.
     *
     * @return array The width and height values.
     */

    public function getDimensions(): array;


    /**
     * Get intermidiate locations.
     *
     * @return array The intermidiate locations.
     */
    public function getIntermidiateLocation(): array;

    /**
     * Get the width.
     *
     * @return int|string|null The width value, which can be an integer, string, or null.
     */
    public function getWidth(): int|string|null;

    /**
     * Set the width.
     *
     * @param int $width The width value.
     */

    public function setWidth(int $width): void;

    /**
     * Get the height.
     *
     * @return int|string|null The height value, which can be an integer, string, or null.
     */
    public function getHeight(): int|string|null;

    /**
     * Set the height.
     *
     * @param int $height The height value.
     */
    public function setHeight(int $height): void;

    /**
     * Factory method for creating an instance of CreateContractReturn.
     *
     * @param int $id The ID of the contract.
     * @param int|string|bool|null $height The height, which can be an integer, string, boolean, or null.
     * @param int|string|bool|null $width The width, which can be an integer, string, boolean, or null.
     * @return self Returns an instance of CreateContractReturn.
     */
    public static function factory(int $id, int|string|bool|null $height, int|string|bool|null $width): self;
}
