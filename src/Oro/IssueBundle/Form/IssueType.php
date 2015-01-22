<?php

namespace Oro\IssueBundle\Form;

use Doctrine\ORM\EntityRepository;
use Oro\IssueBundle\Entity\Issue;
use Oro\ProjectBundle\Entity\Project;
use Oro\ProjectBundle\Entity\ProjectRepository;
use Oro\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class IssueType extends AbstractType
{
    protected $securityContext;

    protected $projectRepository;

    public function __construct(SecurityContextInterface $securityContext, ProjectRepository $projectRepository)
    {
        $this->securityContext = $securityContext;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUser = $this->securityContext->getToken()->getUser();
        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            $currentUser = null; //all projects will be available
        }
        $builder->add('project', 'entity', array(
                    'class' => 'Oro\ProjectBundle\Entity\Project',
                    'query_builder' => function(EntityRepository $entityRepository) use ($currentUser) {
                            return $entityRepository->getProjectsByMember($currentUser);
                        },
                    'property' => 'fullLabel',
                    'empty_value' => 'Select Project',
                )
            )
            ->add('summary', 'text')
            ->add('description', 'textarea')
            ->add('type', 'choice', array(
                'choices'   => array(
                    Issue::ISSUE_TYPE_BUG => Issue::ISSUE_TYPE_BUG,
                    Issue::ISSUE_TYPE_SUBTASK => Issue::ISSUE_TYPE_SUBTASK,
                    Issue::ISSUE_TYPE_TASK => Issue::ISSUE_TYPE_TASK,
                    Issue::ISSUE_TYPE_STORY => Issue::ISSUE_TYPE_STORY,
                ),
                'required'  => true,
            ))
            ->add('parent', 'hidden', array('data' => null))
            ->add('priority', 'choice', array(
                'choices'   => array(
                    Issue::ISSUE_PRIORITY_BLOCKER => Issue::ISSUE_PRIORITY_BLOCKER,
                    Issue::ISSUE_PRIORITY_CRITICAL => Issue::ISSUE_PRIORITY_CRITICAL,
                    Issue::ISSUE_PRIORITY_MAJOR => Issue::ISSUE_PRIORITY_MAJOR,
                    Issue::ISSUE_PRIORITY_MINOR => Issue::ISSUE_PRIORITY_MINOR,
                ),
                'required'  => true,
            ))
            ->add('assignee', 'hidden', array('data' => null))
            ->add('save', 'submit', array('label' => 'Create'));

        $formModifier = function (FormInterface $form, $data) {
            if (is_array($data)) {
                $type = isset($data['type']) ? $data['type'] : null;
                $projectId = isset($data['project']) ? $data['project'] : null;
            } else {
                $type = $data->getType();
                $projectId = $data->getProject() ? $data->getProject()->getId() : null;
            }

            if ($type == Issue::ISSUE_TYPE_SUBTASK && !empty($projectId)) {
                $form->add('parent', 'entity', array(
                    'class' => 'Oro\IssueBundle\Entity\Issue',
                    'query_builder' => function(EntityRepository $entityRepository) use ($projectId) {
                            return $entityRepository->getStoriesByProject($projectId);
                        },
                    'property' => 'summary'
                ));
            }

            if (!empty($projectId)) {
                $project = $this->projectRepository->find($projectId);
                $choices = $project ? $project->getMembers() : array();
                $form->add('assignee', 'entity', array(
                    'class' => 'Oro\UserBundle\Entity\User',
                    'choices' => $choices,
                    'property' => 'fullname',
                ));
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data);
            }
        );

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $formModifier($event->getForm(), $data);
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
            'data_class' => 'Oro\IssueBundle\Entity\Issue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'issue_create';
    }
}
