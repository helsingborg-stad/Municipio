<?php

declare(strict_types=1);

namespace Municipio\KulturkortetQRCodeViewer\MunicipioAuth\controller;

use Municipio\KulturkortetQRCodeViewer\MunicipioAuth\views\MunicipioAuthViewFactoryInterface;

interface MunicipioAuthControllerInterface
{
    public function getHomeUrl(): string;

    public function render(MunicipioAuthViewFactoryInterface $viewFactory): string;
}
