<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use Municipio\Helper\WpService;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\FieldValue\RegisterFieldValueInterface;
use PHPUnit\Framework\MockObject\MockObject;
use WpService\Implementations\FakeWpService;

class FormFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        WpService::set($this->getWpService());
        parent::setUp();
    }

    private function getRegisterFieldValue(): RegisterFieldValueInterface|MockObject
    {
        return $this->createMock(RegisterFieldValueInterface::class);
    }

    private function getWpService(): FakeWpService|MockObject
    {
        return new FakeWpService([
            'addFilter' => true,
            '__'        => fn($string) => $string
        ]);
    }

    public function testCreateFormWithPlaceSchema()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::place());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithProjectSchema()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::project());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithJobPostingSchema()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::jobPosting());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithSpecialAnnouncementSchema()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::specialAnnouncement());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithEventSchema()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::event());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithUnknownSchemaType()
    {
        $formFactory = new FormFactory($this->getRegisterFieldValue(), $this->getWpService());
        $form        = $formFactory->createForm(Schema::zoo());

        $this->assertEmpty($form);
    }
}
