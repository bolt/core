<?php

namespace Bolt\Tests\Controller\Backend;

use Bolt\Entity\User;
use Bolt\Tests\DbAwareTestCase;

class ContentEditControllerTest extends DbAwareTestCase
{

    public function testCreateNewContent(): void
    {
        $admin = $this->getEm()->getRepository(User::class)->findOneByUsername('admin');
        $this->client->loginUser($admin);

        // test controller
        $this->client->followRedirects(true);
        $contentTypeName = 'simpletests';
        $crawler = $this->client->request('GET', "/bolt/new/$contentTypeName");
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('#field-id');

        // the form containing the actual content has id 'editcontent'
        // HOWEVER, it has almost no actual content, because we need javascript for that
        $form = $crawler->filter('#editcontent')->form();
        $values = $form->getValues();

        // get csrf token from form -- lots of things are not in the form as they need javascript to run,
        // but the _csrf_token is present in the 'plain' html in the form
        $postcontent = [
//            "_csrf_token" => "2115b.RPGOucNOr6AB8BqbunzVAn7THveddYkhitjSyqKjixY.fKDk9IUGysJtl2PO-z26MQ-DWJroFP9ZybWUvuWXxn9ywNHMsiftxjmXLg",
            "_csrf_token" => $values["_csrf_token"],
            "_edit_locale" => "en",
            "fields[title]" => "titlecontent",
//            "fields[teaser]" => "teasercontent",
//            "fields[image][media]" => "",
//            "fields[image][filename]" => "",
//            "fields[image][alt]" => "",
//            "fields[image][]" => "",
//            "fields[body]" => "",
            "fields[slug]" => "titlecontent",
//            "fields[template]" => '[""]',
//            "taxonomy[groups]" => '["main"]',
            "save" => "",
            "status" => '["published"]',
            "publishedAt" => "",
            "depublishedAt" => "",
        ];

        // we cannot use the 'original' form to post, as it doesn't have all the fields we need
//        $this->client->submit($form, $postcontent);

        // so we 'fake' page interaction by POSTing directly
        $crawler = $this->client->request('POST', "/bolt/new/$contentTypeName", $postcontent);

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('#field-id');

//        echo $crawler->filter('body')->html();
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
