<?php

namespace Twinpeaks\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Twinpeaks\UserBundle\Entity\UserGroup;
use Twinpeaks\UserBundle\Form\UserGroupType;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * UserGroup controller.
 *
 * @Route("/admin/usergroup")
 */
class UserGroupController extends Controller
{

    /**
     * Lists all UserGroup entities.
     *
     * @Route("/", name="usergroup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TPUserBundle:UserGroup')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new UserGroup entity.
     *
     * @Route("/", name="usergroup_create")
     * @Method("POST")
     * @Template("TPUserBundle:UserGroup:new.html.twig")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function createAction(Request $request)
    {
        $entity  = new UserGroup();
        $form = $this->createUpdateForm($entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usergroup_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new UserGroup entity.
     *
     * @Route("/new", name="usergroup_new")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function newAction()
    {
        $entity  = new UserGroup();
        $form = $this->createUpdateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a UserGroup entity.
     *
     * @Route("/{id}", name="usergroup_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TPUserBundle:UserGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserGroup entity.
     *
     * @Route("/{id}/edit", name="usergroup_edit")
     * @Method("GET")
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TPUserBundle:UserGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGroup entity.');
        }
        
        $editForm = $this->createUpdateForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing UserGroup entity.
     *
     * @Route("/{id}", name="usergroup_update")
     * @Method("PUT")
     * @Template("TPUserBundle:UserGroup:edit.html.twig")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TPUserBundle:UserGroup')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserGroup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createUpdateForm($entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usergroup_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a UserGroup entity.
     *
     * @Route("/{id}", name="usergroup_delete")
     * @Method("DELETE")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TPUserBundle:UserGroup')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserGroup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usergroup'));
    }

    /**
     * Creates a form to delete a UserGroup entity by id.
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
     * Creates a form to create or update the entity.
     *
     * @param mixed $entity 
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createUpdateForm(\Twinpeaks\UserBundle\Entity\UserGroup $entity)
    {
        $options = array(
            'roles' => $this->container->getParameter('security.role_hierarchy.roles'),
            'security' => $this->get('security.context'),
        );
        
        return $this->createForm(new UserGroupType(), $entity, $options);        
    }
}
