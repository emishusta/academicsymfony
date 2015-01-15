<?php

namespace Oro\ProjectBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('label', 'text')
            ->add('summary', 'textarea')
            ->add('code', 'text')
            ->add('members', 'entity', array(
                    'class' => 'Oro\UserBundle\Entity\User',
                    'property' => 'fullname',
                    'multiple' => true,
                )
            )
            ->add('save', 'submit', array('label' => 'Create'));
    }

    /**
     * Set default options for this form type
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'data_class' => 'Oro\ProjectBundle\Entity\Project'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'project_create';
    }
}
