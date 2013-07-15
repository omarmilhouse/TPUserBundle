<?php

namespace Twinpeaks\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    const FORM_TYPE_ADMIN = 'a';
    const FORM_ADMIN_CONFIRM = 'ac';

    private $options;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->options = $options;
        $type = $options['type'];
        $user = $options['data'];
        
        switch ($type) {
            case self::FORM_TYPE_ADMIN:
                $builder
                    ->add('email');
                
                if(!$user->getId()) {
                    $builder
                        ->add('password', 'password');
                }
                
                $builder
                    ->add('firstName', null, array('label'=> 'Nome'))
                    ->add('lastName', null, array('label'=> 'Cognome'))
                    ->add('groups',  null, array('label'=> 'Gruppi'));
                
                break;
                
            case self::FORM_ADMIN_CONFIRM:
                $builder
                    ->add('email')
                    ->add('firstName', null, array('label'=> 'Nome'))
                    ->add('lastName', null, array('label'=> 'Cognome'))
                    ->add('groups',  null, array('label'=> 'Gruppi'));
                
                break;

            default:
                break;
        }
        
        $builder->add('_type', 'hidden', array(
            'mapped' => false,
            'data' => $type,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Twinpeaks\UserBundle\Entity\User',
            'type' => self::FORM_TYPE_ADMIN,
        ));
    }

    public function getName()
    {
        return 'Twinpeaks_corebundle_usertype';
    }
}
