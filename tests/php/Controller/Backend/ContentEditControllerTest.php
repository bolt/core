<?php

namespace Bolt\Tests\Controller\Backend;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Bolt\Repository\ContentRepository;
use Bolt\Tests\DbAwareTestCase;
use Bolt\Twig\FieldExtension;
use Tightenco\Collect\Support\Collection;

class ContentEditControllerTest extends DbAwareTestCase
{
    public function testCreateNewComplexNestedContent(): void
    {
        // (2) use self::$container to access the service container
        $container = self::$container;

        $admin = $this->getEm()->getRepository(User::class)->findOneByUsername('admin');
        $this->client->loginUser($admin);

        /** @var ContentRepository $contentRepositoryBefore */
        $contentRepositoryBefore = $container->get(ContentRepository::class);
        $contentCount = $contentRepositoryBefore->count([]);

        // test controller
        $this->client->followRedirects(true);
        $contentTypeName = 'complexnestedtype';
        $crawler = $this->client->request('GET', "/bolt/new/$contentTypeName");
        self::assertResponseIsSuccessful();

        // the form containing the actual content has id 'editcontent'
        // HOWEVER, it has almost no actual content, because we need javascript for that
        $form = $crawler->filter('#editcontent')->form();
        $values = $form->getValues();

        // get csrf token from form -- lots of things are not in the form as they need javascript to run,
        // but the _csrf_token is present in the 'plain' html in the form
        // NOTE ugly code because data was dumped using debugger and
        $postContent = array (
            '_csrf_token' => $values["_csrf_token"],
            '_edit_locale' => 'en',
            'fields' =>
                array (
                    'first_field' => '["een"]',
                ),
            'collections' =>
                array (
                    'second_field' =>
                        array (
                            'first_collection_field' =>
                                array (
                                    '622e624a526f0' =>
                                        array (
                                            'first_set_field' => '["beeldvullend"]',
                                        ),
                                ),
                            'order' =>
                                array (
                                    0 => '622e624a526f0',
                                ),
                        ),
                ),
            'keys-collections' =>
                array (
                    'second_field' =>
                        array (
                            'first_collection_field' =>
                                array (
                                    '622e624a526f0' =>
                                        array (
                                            'first_set_field' => '0',
                                        ),
                                ),
                        ),
                ),
            'save' => '',
            'status' => '["published"]',
            'publishedAt' => '',
            'depublishedAt' => '',
        );

        // 'fake' page interaction by POSTing directly
        $crawler = $this->client->request('POST', "/bolt/new/$contentTypeName", $postContent);

        self::assertResponseIsSuccessful();

        $contentCountAfter = $contentRepositoryBefore->count([]);
        self::assertEquals(1, $contentCountAfter - $contentCount, 'There should be one new content item');

        /** @var ContentRepository $contentRepository */
        $contentRepository = $container->get(ContentRepository::class);
        // get the latest content item created
        $records = $contentRepository->findBy([], ['id' => 'DESC'], 1);

        $record = $records[0];
        self::assertNotNull($record);

        // check the fields?
        /** @var FieldExtension $fieldExtension */
        $fieldExtension = $container->get(FieldExtension::class);

        /** @var Field $afbeeldingenTemplateField */
        $afbeeldingenTemplateField = $record->getField('first_field');
        $afbeeldingenTemplateFieldOptions = $fieldExtension->selectOptions($afbeeldingenTemplateField);
        // when required=true
        self::assertEquals(5, $afbeeldingenTemplateFieldOptions->count(), 'expected 5 select options for first_field (required=true)');
        // when required=false
//        self::assertEquals(6, $afbeeldingenTemplateFieldOptions->count(), 'expected 6 select options for first_field (required=false)');


        // check the select field
        /** @var Field\CollectionField $popupsField */
        $popupsField = $record->getField('second_field');
        $nestedFields = $popupsField->getValue();

        /** @var Field\SetField $setField */
        $setField = $nestedFields[0];

        $nestedSetFields = $setField->getValue();

        /** @var Field $nestedSetField */
        foreach ($nestedSetFields as $nestedSetField) {
            if ($nestedSetField->getName() === 'first_set_field') {
                /** @var Collection $options */
                $options = $fieldExtension->selectOptions($nestedSetField);
                self::assertEquals(2, $options->count(), 'expected 2 select options');
            }
        }
    }

    /*
    public function testOpenExistingContentType(): void
    {
        // TODO insert content...
        // ...and get id
        $id = 123;

        $admin = $this->getEm()->getRepository(User::class)->findOneByUsername('admin');
        $this->client->loginUser($admin);

        // test controller
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', "/bolt/edit/$id");
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#field-id');
    }
    */
}
