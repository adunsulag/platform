<?php

namespace Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type;

use Symfony\Component\Form\PreloadedExtension;

use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\LocaleBundle\Form\Type\FormattingSelectType;
use Oro\Bundle\LocaleBundle\Form\Type\LanguageSelectType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizationType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizationParentSelectType;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type\Stub\FormattingSelectTypeStub;
use Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type\Stub\LanguageSelectTypeStub;
use Oro\Bundle\LocaleBundle\Tests\Unit\Form\Type\Stub\LocalizedFallbackValueCollectionTypeStub;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Component\Testing\Unit\FormIntegrationTestCase;
use Oro\Component\Testing\Unit\Form\Type\Stub\EntityType;

class LocalizationTypeTest extends FormIntegrationTestCase
{
    use EntityTrait;

    const DATA_CLASS = 'Oro\Bundle\LocaleBundle\Entity\Localization';

    /**
     * @var LocalizationType
     */
    protected $formType;

    /**
     * @return array
     */
    protected function getExtensions()
    {
        return [
            new PreloadedExtension(
                [
                    LocalizedFallbackValueCollectionType::NAME => new LocalizedFallbackValueCollectionTypeStub(),
                    LanguageSelectType::NAME => new LanguageSelectTypeStub(),
                    FormattingSelectType::NAME => new FormattingSelectTypeStub(),
                    LocalizationParentSelectType::NAME => new EntityType([], LocalizationParentSelectType::NAME),
                ],
                []
            )
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->formType = new LocalizationType();
        $this->formType->setDataClass(static::DATA_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->formType);

        parent::tearDown();
    }

    public function testGetName()
    {
        $this->assertInternalType('string', $this->formType->getName());
        $this->assertEquals(LocalizationType::NAME, $this->formType->getName());
    }

    public function testBuildForm()
    {
        $form = $this->factory->create($this->formType);

        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('titles'));
        $this->assertTrue($form->has('languageCode'));
        $this->assertTrue($form->has('formattingCode'));
        $this->assertTrue($form->has('parentLocalization'));
    }

    /**
     * @dataProvider submitDataProvider
     * @param mixed $defaultData
     * @param array $submittedData
     * @param array $expectedData
     */
    public function testSubmit($defaultData, array $submittedData, $expectedData)
    {
        $form = $this->factory->create($this->formType, $defaultData);

        $formConfig = $form->getConfig();
        $this->assertEquals(static::DATA_CLASS, $formConfig->getOption('data_class'));

        $this->assertEquals($defaultData, $form->getData());
        $this->assertEquals($defaultData, $form->getViewData());

        $form->submit($submittedData);
        $this->assertTrue($form->isValid());
        $this->assertEquals($expectedData, $form->getData());
    }

    /**
     * @return array
     */
    public function submitDataProvider()
    {
        $localizationItem = $this->createLocalization('name', 'title', 'en', 'en');
        $parentLocalization = $this->getEntity('Oro\Bundle\LocaleBundle\Entity\Localization', ['id' => 1]);

        return [
            'without entity' => [
                'defaultData' => null,
                'submittedData' => [
                    'name' => 'NAME',
                    'titles' => [['string' => 'TITLE']],
                    'languageCode' => 'ru',
                    'formattingCode' => 'ru',
                    'parentLocalization' => null,
                ],
                'expectedData' => $this->createLocalization('NAME', 'TITLE', 'ru', 'ru'),
            ],
            'with entity' => [
                'defaultData' => $localizationItem,
                'submittedData' => [
                    'name' => 'new_localization_item_name',
                    'titles' => [['string' => 'new_localization_item_title']],
                    'languageCode' => 'en_US',
                    'formattingCode' => 'en_US',
                    'parentLocalization' => '1',
                ],
                'expectedData' => $this->createLocalization(
                        'new_localization_item_name',
                        'new_localization_item_title',
                        'en_US',
                        'en_US',
                        $parentLocalization
                    ),
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $languageCode
     * @param string $formattingCode
     * @param mixed $id
     * @param Localization $parentLocalization
     * @return Localization
     */
    protected function createLocalization($name, $title, $languageCode, $formattingCode, $id = null, Localization $parentLocalization = null)
    {
        $localization = new Localization();
        $localization->setName($name);
        $localization->setDefaultTitle($title);
        $localization->setLanguageCode($languageCode);
        $localization->setFormattingCode($formattingCode);
        $localization->setParentLocalization($parentLocalization);

        return $localization;
    }
}