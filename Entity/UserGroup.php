<?php

namespace Twinpeaks\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * @ORM\Table(name="tp_group")
 * @ORM\Entity()
 */
class UserGroup extends BaseGroup {
    
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    protected $default;

    public function __construct($name = '', $roles = array())
    {
        parent::__construct($name, $roles);
        $this->default = false;
    }   
    
    public function __toString() {
        return $this->name;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Check if is default
     *
     * @return boolean
     */     
    public function isDefault() {
        return $this->default;
    }
    
    /**
     * Set default
     *
     * @param boolean $default 
     * @return UserGroup
     */    
    public function setDefault($default) {
        $this->default = $default;
        
        return $this;
    }
}