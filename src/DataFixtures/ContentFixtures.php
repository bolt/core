<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Bolt\Utils\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ContentFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var \Faker\Generator */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadContent($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$fullname, $username, $password, $email, $roles]) {
            $user = new User();
            $user->setFullName($fullname);
            $user->setUsername($username);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
            $user->setEmail($email);
            $user->setRoles($roles);

            $manager->persist($user);
            $this->addReference($username, $user);
        }

        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            // $userData = [$fullname, $username, $password, $email, $roles];
            ['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
            ['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
            ['John Doe', 'john_user', 'kitten', 'john_user@symfony.com', ['ROLE_USER']],
        ];
    }

    private function loadContent(ObjectManager $manager)
    {
        foreach (range(1, 15) as $i) {
            $author = $this->getReference(['jane_admin', 'tom_admin'][0 === $i ? 0 : random_int(0, 1)]);

            $content = new Content();
            $content->setContenttype($this->getRandomContentType());
            $content->setAuthor($author);
            $content->setStatus($this->getRandomStatus());
            $content->setCreatedAt($this->faker->dateTimeBetween('-1 year'));
            $content->setModifiedAt($this->faker->dateTimeBetween('-1 year'));
            $content->setPublishedAt($this->faker->dateTimeBetween('-1 year'));
            $content->setDepublishedAt($this->faker->dateTimeBetween('-1 year'));

            /*
             * * id
             * contenttype `['pages', 'entries', 'homepage', 'blocks', 'showcases']`
             * author_id
             * status `['published', 'held', 'draft', 'timed']`
             * created_at
             * modified_at
             * published_at
             * depublished_at
             */

            foreach (range(1, 5) as $i) {
                $fieldtype = $this->getRandomFieldType();

                $field = new Field();
                $field->setName($this->faker->word());
                $field->setType($fieldtype);
                $field->setValue($this->getValuesforFieldType($fieldtype));
                $field->setSortorder($i * 5);

                $content->addField($field);

                /*
                 * * id
                 * content_id
                 * name
                 * type `['text', 'textarea', 'html', 'markdown', 'image']`
                 * value (JSON)
                 * parent_id
                 * sortorder
                 * (later) locale
                 * (later) version
                 */
            }

            $manager->persist($content);
        }
    }

    private function getRandomContentType()
    {
        $contentTypes = ['pages', 'entries', 'homepage', 'blocks', 'showcases'];

        return $contentTypes[array_rand($contentTypes)];
    }

    private function getRandomFieldType()
    {
        $fieldTypes = ['slug', 'text', 'textarea', 'html', 'markdown', 'image'];

        return $fieldTypes[array_rand($fieldTypes)];
    }

    private function getRandomStatus()
    {
        $statuses = ['published', 'held', 'draft', 'timed'];

        return $statuses[array_rand($statuses)];
    }

    private function getValuesforFieldType($type)
    {
        switch ($type) {
            case 'html':
            case 'textarea':
            case 'markdown':
                $data = ['value' => $this->faker->paragraphs(3, true)];
                break;
            case 'image':
                $data = ['filename' => 'kitten.jpg', 'alt' => 'A cute kitten'];
                break;
            case 'slug':
                $data = ['value' => Slugger::slugify($this->faker->sentence(3, true))];
                break;
            default:
                $data = ['value' => $this->faker->sentence(6, true)];
        }

        return $data;
    }
}
