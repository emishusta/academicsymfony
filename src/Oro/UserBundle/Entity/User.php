<?php

namespace Oro\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $fullname;

    /**
     * @ORM\Column(name="avatar_path", type="string", length=255, nullable=true)
     */
    protected $avatarPath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $role;

    /**
     * @Assert\File(maxSize="3000000")
     */
    private $avatarFile;

    /**
     * Temporary container for avatar path
     *
     * @var string
     */
    private $avatarTemp;

    /**
     * Set default values
     */
    public function __construct()
    {
        $this->salt = sha1(uniqid(mt_rand(), true));
        $this->roles = array();
    }

    /**
     * Returns the roles granted to the user.
     *
     * @return array The user roles
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get User ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get User email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * User class serialization
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->salt,
        ));
    }

    /**
     * User class deserialization
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set User role
     *
     * @param $role
     * @return $this
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAvatarAbsolutePath()
    {
        return null === $this->avatarPath
            ? null
            : $this->getUploadRootDir().'/'.$this->id.'.'.$this->avatarPath;
    }

    /**
     * @return null|string
     */
    public function getAvatarWebPath()
    {
        return null === $this->avatarPath
            ? null
            : $this->getUploadDir().'/'.$this->id.'.'.$this->avatarPath;
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web'.$this->getUploadDir();
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return '/uploads/avatars';
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setAvatarFile(UploadedFile $file = null)
    {
        $this->avatarFile = $file;
        // check if we have an old image path
        if (is_file($this->getAvatarAbsolutePath())) {
            // store the old name to delete after the update
            $this->avatarTemp = $this->getAvatarAbsolutePath();
        }

        $this->avatarPath = 'initial';
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getAvatarFile()) {
            $this->avatarPath = $this->getAvatarFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getAvatarFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->avatarTemp)) {
            // delete the old image
            unlink($this->avatarTemp);
            // clear the temp image path
            $this->avatarTemp = null;
        }

        $this->getAvatarFile()->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->getAvatarFile()->guessExtension()
        );

        $this->setAvatarFile(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->avatarTemp = $this->getAvatarAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->avatarTemp)) {
            unlink($this->avatarTemp);
        }
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return array($this->getRole());
    }
}
