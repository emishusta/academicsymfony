<?php

namespace Oro\IssueBundle\Form;

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
                    'OPEN' => 'OPEN',
                    'IN_PROGRESS' => 'IN_PROGRESS',
                    'CLOSED' => 'CLOSED',
                ),
                'required'  => true,
            ))
            ->add('resolution', 'choice', array(
                'choices'   => array(
                    'UNRESOLVED' => 'UNRESOLVED',
                    'FIXED' => 'FIXED',
                    'INCOMPLETE' => 'INCOMPLETE',
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
