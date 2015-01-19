<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Oro\IssueBundle\Entity\IssueRepository")
 * @ORM\Table(name="oro_issue")
 * @ORM\HasLifecycleCallbacks()
 */
class Issue
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $summary;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $priority;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $resolution;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User", inversedBy="reporterIssues")
     */
    protected $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User", inversedBy="assignedIssues")
     */
    protected $assignee;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     **/
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Issue", mappedBy="parent")
     **/
    protected $children;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\ProjectBundle\Entity\Project", inversedBy="issues")
     */
    protected $project;

    /**
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Comment", mappedBy="issue")
     **/
    protected $comments;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->status = 'OPEN';
        $this->resolution = 'UNRESOLVED';
    }

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
     * Set code
     *
     * @param string $code
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Issue
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
     * Set priority
     *
     * @param string $priority
     * @return Issue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Issue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set resolution
     *
     * @param string $resolution
     * @return Issue
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return string 
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Issue
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return Issue
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set reporter
     *
     * @param \Oro\UserBundle\Entity\User $reporter
     * @return Issue
     */
    public function setReporter(\Oro\UserBundle\Entity\User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \Oro\UserBundle\Entity\User 
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param \Oro\UserBundle\Entity\User $assignee
     * @return Issue
     */
    public function setAssignee(\Oro\UserBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Oro\UserBundle\Entity\User 
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set parent
     *
     * @param \Oro\IssueBundle\Entity\Issue $parent
     * @return Issue
     */
    public function setParent(\Oro\IssueBundle\Entity\Issue $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Oro\IssueBundle\Entity\Issue 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Oro\IssueBundle\Entity\Issue $children
     * @return Issue
     */
    public function addChild(\Oro\IssueBundle\Entity\Issue $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Oro\IssueBundle\Entity\Issue $children
     */
    public function removeChild(\Oro\IssueBundle\Entity\Issue $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set project
     *
     * @param \Oro\ProjectBundle\Entity\Project $project
     * @return Issue
     */
    public function setProject(\Oro\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Oro\ProjectBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     *
     * @return $this
     */
    public function updateCode()
    {
        $this->setCode($this->getProject()->getCode() . '-' . $this->getId());

        return $this;
    }

    /**
     * @ORM\PreUpdate
     *
     * @return $this
     */
    public function updateUpdatedDate()
    {
        $this->setUpdated(new \DateTime());

        return $this;
    }

    /**
     * Add comments
     *
     * @param \Oro\IssueBundle\Entity\Comment $comments
     * @return Issue
     */
    public function addComment(\Oro\IssueBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Oro\IssueBundle\Entity\Comment $comments
     */
    public function removeComment(\Oro\IssueBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId();
    }
}
