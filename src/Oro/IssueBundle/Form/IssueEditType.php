<?php

namespace Oro\IssueBundle\Form;

use Oro\IssueBundle\Entity\Issue;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class IssueEditType extends IssueType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('type')
            ->remove('project')
            ->remove('save')
            ->add('status', 'choice', array(
                'choices'   => array(
                    Issue::ISSUE_STATUS_OPEN => Issue::ISSUE_STATUS_OPEN,
                    Issue::ISSUE_STATUS_IN_PROGRESS => Issue::ISSUE_STATUS_IN_PROGRESS,
                    Issue::ISSUE_STATUS_CLOSED => Issue::ISSUE_STATUS_CLOSED,
                ),
                'required'  => true,
            ))
            ->add('resolution', 'choice', array(
                'choices'   => array(
                    Issue::ISSUE_RESOLUTION_UNRESOLVED => Issue::ISSUE_RESOLUTION_UNRESOLVED,
                    Issue::ISSUE_RESOLUTION_FIXED => Issue::ISSUE_RESOLUTION_FIXED,
                    Issue::ISSUE_RESOLUTION_INCOMPLETE => Issue::ISSUE_RESOLUTION_INCOMPLETE,
                ),
                'required'  => true,
            ))
            ->add('save', 'submit', array('label' => 'Update'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'issue_update';
    }
}
