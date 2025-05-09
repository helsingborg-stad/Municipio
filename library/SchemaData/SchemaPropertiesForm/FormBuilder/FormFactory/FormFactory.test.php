<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;

use PHPUnit\Framework\TestCase;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\FormFactory;
use Municipio\Schema\BaseType;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\StringField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\UrlField;
use Municipio\SchemaData\SchemaPropertiesForm\FormBuilder\Fields\GeoCoordinatesField;

class FormFactoryTest extends TestCase
{
    private FormFactory $formFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formFactory = new FormFactory();
    }

    public function testCreateFormWithPlaceSchema()
    {
        $form = $this->formFactory->createForm(Schema::place());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithProjectSchema()
    {
        $form = $this->formFactory->createForm(Schema::project());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithJobPostingSchema()
    {
        $form = $this->formFactory->createForm(Schema::jobPosting());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithSpecialAnnouncementSchema()
    {
        $form = $this->formFactory->createForm(Schema::specialAnnouncement());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithEventSchema()
    {
        $form = $this->formFactory->createForm(Schema::event());

        $this->assertNotEmpty($form['fields']);
    }

    public function testCreateFormWithUnknownSchemaType()
    {
        $form = $this->formFactory->createForm(Schema::zoo());

        $this->assertEmpty($form['fields']);
    }

    public function testPrepareFieldName()
    {
        $this->assertEquals('schema_example', $this->formFactory->prepareFieldName('example'));
    }
}
