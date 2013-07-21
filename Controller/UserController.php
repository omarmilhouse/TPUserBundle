<?php

namespace Twinpeaks\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Twinpeaks\UserBundle\Entity\User;
use Twinpeaks\UserBundle\Form\UserType;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Controller\ResettingController;

/**
 * User controller.
 */
class UserController extends Controller
{
    
    /**
     * Lists all User entities.
     *
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql   = "SELECT u FROM TPUserBundle:User u";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10/*limit per page*/
        );        
        
//        $entities = $em->getRepository('TPUserBundle:User')->findAll();
//
        return array(
            //'entities' => $entities,
            'pagination' => $pagination,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Template("TPUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new User();
        $userForm = new UserType();
        $params = $this->getRequest()->get($userForm->getName());
        $type = $params['_type'];
        
        $form = $this->createForm(new UserType(), $entity, array('type' => $type));
        
        $form->bind($request);

        if ($form->isValid()) {
            
            if($type == UserType::FORM_ADMIN_CONFIRM) {
                $entity->setPassword($this->generateRandomPassword());
            }
            
            $manipulator = $this->get('fos_user.util.user_manipulator');
            $user = $manipulator->create($entity->getEmail(), $entity->getPassword(), $entity->getEmail(), true, false);            
            
            $user->setFirstName($entity->getFirstName());
            $user->setLastName($entity->getLastName());
            
            foreach ($entity->getGroups() as $group) {
                $user->addGroup($group);
            }
            
            
            if($type == UserType::FORM_ADMIN_CONFIRM) {
                $user->setEnabled(false);
                
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());                
                
                //$this->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
                $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
                $user->setPasswordRequestedAt(new \DateTime());
            }
            
            $this->get('fos_user.user_manager')->updateUser($user, false);
            
            //$em->persist($user);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            return $this->redirect($this->generateUrl('tp_user_show', array('id' => $user->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    
    /**
     * Displays a form to create a new User entity with confirm.
     *
     * @Template("TPUserBundle:User:new.html.twig")
     */
    public function newConfirmAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, array('type' => UserType::FORM_ADMIN_CONFIRM));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        
        //return $this->render('TPUserBundle:User:new.html.twig', $data);
    }

    /**
     * Finds and displays a User entity.
     *
     * @Template()
     */
    public function showAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TPUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TPUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createForm(new UserType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing User entity.
     *
     * @Template("TPUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TPUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new UserType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $this->get('fos_user.user_manager')->updateUser($entity, false);
            //$em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tp_user_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TPUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tp_user'));
    }
    
    /**
     * Batch deletes User entities.
     *
     */
    public function batchDeleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('TPUserBundle:User');
        $values = $request->get('batch');
        
        foreach ($values as $id) {
            $user = $repo->find($id);
            
            try {
              $em->remove($user);
            } catch (Exception $exc) {
                
            }
        }
        
        $em->flush();

        return $this->redirect($this->generateUrl('tp_user'));
//        $form = $this->createDeleteForm($id);
//        $form->bind($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $entity = $em->getRepository('TPUserBundle:User')->find($id);
//
//            if (!$entity) {
//                throw $this->createNotFoundException('Unable to find User entity.');
//            }
//
//            $em->remove($entity);
//            $em->flush();
//        }
//
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * Generate temporary random password
     *
     * @param int length
     * 
     * @return string
     */
    private function generateRandomPassword($length = 8)
    {
        $user_id = $this->getUser()->getId();
        $user = $this->getDoctrine()->getRepository('TPUserBundle:User')->find($user_id);
        //$value = uniqid($user->getEmail());
        $value = uniqid($this->getUser()->getEmail());
        return substr(sha1($value), 0, $length - 1);
    }
}
