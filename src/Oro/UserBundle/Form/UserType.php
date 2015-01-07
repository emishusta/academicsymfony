<?php

namespace Oro\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text')
            ->add('email', 'email')
            ->add('fullname', 'text')
            ->add('password', 'repeated', array(
                    'type' => 'password',
                    'required' => true,
                    'invalid_message' => 'The password fields must match.',
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                )
            )
            ->add('role', 'choice', array(
                'choices'   => array(
                    'ROLE_USER' => 'Operator', //TODO: change with translations later
                    'ROLE_ADMIN' => 'Manager',
                    'ROLE_SUPER_ADMIN' => 'Administrator'
                ),
                'required'  => true,
            ))
            ->add('avatar_file', 'file')
            ->add('save', 'submit', array('label' => 'Create User'));
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
            'data_class' => 'Oro\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'user_new';
    }
}
