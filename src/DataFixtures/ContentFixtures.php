<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Bolt\Utils\Slugger;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ContentFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
            $content->setCreatedAt(new \DateTime('now - ' . rand(1, 9000) . 'hours'));
            $content->setModifiedAt(new \DateTime('now - ' . rand(1, 9000) . 'hours'));
            $content->setPublishedAt(new \DateTime('now - ' . rand(1, 9000) . 'hours'));
            $content->setDepublishedAt(new \DateTime('now - ' . rand(1, 9000) . 'hours'));

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
                $field->setName('title');
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
                $data = ['value' => $this->getRandomText()];
                break;
            case 'image':
                $data = ['filename' => 'kitten.jpg', 'alt' => 'A cute kitten'];
                break;
            case 'slug':
                $data = ['value' => Slugger::slugify($this->getRandomPhrase())];
                break;
            default:
                $data = ['value' => $this->getRandomPhrase()];
        }

        return $data;
    }

    private function getRandomPhrase(): string
    {
        $phrases = $this->getPhrases();

        return $phrases[array_rand($phrases)];
    }

    private function getPhrases(): array
    {
        return [
            'Lorem ipsum dolor sit amet consectetur adipiscing elit',
            'Pellentesque vitae velit ex',
            'Mauris dapibus risus quis suscipit vulputate',
            'Eros diam egestas libero eu vulputate risus',
            'In hac habitasse platea dictumst',
            'Morbi tempus commodo mattis',
            'Ut suscipit posuere justo at vulputate',
            'Ut eleifend mauris et risus ultrices egestas',
            'Aliquam sodales odio id eleifend tristique',
            'Urna nisl sollicitudin id varius orci quam id turpis',
            'Nulla porta lobortis ligula vel egestas',
            'Curabitur aliquam euismod dolor non ornare',
            'Sed varius a risus eget aliquam',
            'Nunc viverra elit ac laoreet suscipit',
            'Pellentesque et sapien pulvinar consectetur',
            'Ubi est barbatus nix',
            'Abnobas sunt hilotaes de placidus vita',
            'Ubi est audax amicitia',
            'Eposs sunt solems de superbus fortis',
            'Vae humani generis',
            'Diatrias tolerare tanquam noster caesium',
            'Teres talis saepe tractare de camerarius flavum sensorem',
            'Silva de secundus galatae demitto quadra',
            'Sunt accentores vitare salvus flavum parses',
            'Potus sensim ad ferox abnoba',
            'Sunt seculaes transferre talis camerarius fluctuies',
            'Era brevis ratione est',
            'Sunt torquises imitari velox mirabilis medicinaes',
            'Mineralis persuadere omnes finises desiderium',
            'Bassus fatalis classiss virtualiter transferre de flavum',
        ];
    }

    private function getRandomText(int $maxLength = 255): string
    {
        $phrases = $this->getPhrases();
        shuffle($phrases);

        while (mb_strlen($text = implode('. ', $phrases) . '.') > $maxLength) {
            array_pop($phrases);
        }

        return $text;
    }
}
