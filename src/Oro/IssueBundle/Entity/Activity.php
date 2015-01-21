<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Oro\IssueBundle\Entity\ActivityRepository")
 * @ORM\Table(name="oro_issue_activity")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
{
    const TYPE_NEW_ISSUE = 'new_issue';
    const TYPE_CHANGED_ISSUE = 'changed_issue';
    const TYPE_NEW_COMMENT = 'new_comment';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\Issue", inversedBy="activities")
     */
    protected $issue;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User", inversedBy="activities")
     */
    protected $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(name="issue_new_status", type="string", length=255, nullable=true)
     */
    protected $issueNewStatus;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Activity
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set issueNewStatus
     *
     * @param string $issueNewStatus
     * @return Activity
     */
    public function setIssueNewStatus($issueNewStatus)
    {
        $this->issueNewStatus = $issueNewStatus;

        return $this;
    }

    /**
     * Get issueNewStatus
     *
     * @return string 
     */
    public function getIssueNewStatus()
    {
        return $this->issueNewStatus;
    }

    /**
     * Set issue
     *
     * @param \Oro\IssueBundle\Entity\Issue $issue
     * @return Activity
     */
    public function setIssue(\Oro\IssueBundle\Entity\Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \Oro\IssueBundle\Entity\Issue 
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set user
     *
     * @param \Oro\UserBundle\Entity\User $user
     * @return Activity
     */
    public function setUser(\Oro\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Oro\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Activity
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \Oro\IssueBundle\Entity\Comment 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Activity
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created = new \DateTime();
    }
}
