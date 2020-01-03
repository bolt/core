<?php


namespace Bolt\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LogRepository")
 * @ORM\Table(name="log")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @ORM\Column(name="context", type="array", nullable=true)
     */
    private $context;

    /**
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(name="level_name", type="string", length=50)
     */
    private $levelName;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="extra", type="array", nullable=true)
     */
    private $extra;

    /**
     * @ORM\Column(name="user", type="array", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="location", type="array", nullable=true)
     */
    private $location;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return Log
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param mixed $context
     * @return Log
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @param mixed $levelName
     * @return Log
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     * @return Log
     */
    public function setExtra($extra)
    {
        dump($extra);
        $this->extra = $extra;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return Log
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param mixed $location
     * @return Log
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return Log
     */
    public function setUser($user)
    {
        dump($user);
        $this->user = $user;
        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }
}