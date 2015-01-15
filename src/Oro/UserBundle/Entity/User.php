<?php

namespace Oro\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Oro\UserBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="oro_user")
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
     * @ORM\Column(type="string", length=255)
     */
    protected $timezone;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

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
     * @ORM\ManyToMany(targetEntity="Oro\ProjectBundle\Entity\Project", mappedBy="members")
     **/
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Issue", mappedBy="reporter")
     */
    protected $reporterIssues;

    /**
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Issue", mappedBy="assignee")
     */
    protected $assignedIssues;

    /**
     * Set default values
     */
    public function __construct()
    {
        $this->salt = sha1(uniqid(mt_rand(), true));
        $this->roles = array();
        $this->projects = new ArrayCollection();
        $this->created = new \DateTime();
        $this->reporterIssues = new ArrayCollection();
        $this->assignedIssues = new ArrayCollection();
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

    /**
     * Set avatarPath
     *
     * @param string $avatarPath
     * @return User
     */
    public function setAvatarPath($avatarPath)
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    /**
     * Get avatarPath
     *
     * @return string 
     */
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    /**
     * Add projects
     *
     * @param \Oro\ProjectBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\Oro\ProjectBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Oro\ProjectBundle\Entity\Project $projects
     */
    public function removeProject(\Oro\ProjectBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Add reporterIssues
     *
     * @param \Oro\IssueBundle\Entity\Issue $reporterIssues
     * @return User
     */
    public function addReporterIssue(\Oro\IssueBundle\Entity\Issue $reporterIssues)
    {
        $this->reporterIssues[] = $reporterIssues;

        return $this;
    }

    /**
     * Remove reporterIssues
     *
     * @param \Oro\IssueBundle\Entity\Issue $reporterIssues
     */
    public function removeReporterIssue(\Oro\IssueBundle\Entity\Issue $reporterIssues)
    {
        $this->reporterIssues->removeElement($reporterIssues);
    }

    /**
     * Get reporterIssues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReporterIssues()
    {
        return $this->reporterIssues;
    }

    /**
     * Add assignedIssues
     *
     * @param \Oro\IssueBundle\Entity\Issue $assignedIssues
     * @return User
     */
    public function addAssignedIssue(\Oro\IssueBundle\Entity\Issue $assignedIssues)
    {
        $this->assignedIssues[] = $assignedIssues;

        return $this;
    }

    /**
     * Remove assignedIssues
     *
     * @param \Oro\IssueBundle\Entity\Issue $assignedIssues
     */
    public function removeAssignedIssue(\Oro\IssueBundle\Entity\Issue $assignedIssues)
    {
        $this->assignedIssues->removeElement($assignedIssues);
    }

    /**
     * Get assignedIssues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssignedIssues()
    {
        return $this->assignedIssues;
    }
}
