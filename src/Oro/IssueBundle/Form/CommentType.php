<?php

namespace Oro\IssueBundle\Form;

use Oro\IssueBundle\Entity\IssueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommentType extends AbstractType
{
    protected $_action;

    protected $_issueRepository;

    public function __construct($action, IssueRepository $issueRepository)
    {
        $this->_issueRepository = $issueRepository;
        $this->_action = $action;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $issueId = isset($options['data']) && $options['data']->getIssue() ? $options['data']->getIssue()->getId() : null;
        $builder->setAction($this->_action)
            ->add('body', 'textarea', array('label' => false))
            ->add('issue', 'entity', array('class' => 'Oro\IssueBundle\Entity\Issue'))
            ->add('issue', 'hidden', array('data' => $issueId))
            ->add('save', 'submit', array('label' => 'Submit'));

        $builder->get('issue')->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                if (is_string($event->getData())) {
                    $event->setData($this->_issueRepository->find((int)$event->getData()));
                }
            }
        );
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
            'data_class' => 'Oro\IssueBundle\Entity\Comment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'issue_comment_create';
    }
}
