<?php

namespace Twinpeaks\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserGroupType extends AbstractType
{
    private $options;


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        
        $builder
            ->add('name')
            ->add('roles', 'choice', array(
                'choices' => $this->getRoles(),
                'required'    => true,
                'empty_value' => 'Seleziona un ruolo',
                'empty_data'  => array(),
                'multiple' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Twinpeaks\UserBundle\Entity\UserGroup',
            'roles' => array(),
            'security' => null,
        ));
    }

    public function getName()
    {
        return 'Twinpeaks_corebundle_usergrouptype';
    }
    
    private function getRoles() {
        $roles = array();
        $security = $this->options['security'];
        //TODO
        if($this->options['roles']) {
            foreach ($this->options['roles'] as $role=>$detail) {
                if(!$security->isGranted($role))
                    continue;
                
                $roles[$role] = $role;
            }
        }
        
        return $roles;
    }
}
