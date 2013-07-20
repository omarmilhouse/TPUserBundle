TPUserBundle
============

FOSUserBundle extension with admin gui

##Installation

Add TPUserBundle in your composer.json, TPUserBundle require also FOSUserBundle:

```js
{
    "require": {
        "friendsofsymfony/user-bundle": "~2.0@dev",
        "twinpeaks/user-bundle": "dev-master"
    }
}
```

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\UserBundle\FOSUserBundle(),
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        new Twinpeaks\UserBundle\TPUserBundle(),
    );
}
```

Configure your application's security.yml

``` yaml
# app/config/security.yml
security:
    encoders:
        FOS\UserBundle\Model\UserInterface: sha512

    role_hierarchy:
        ROLE_USER:        ROLE_USER
        ROLE_ADMIN:       [ROLE_USER]
        ROLE_SUPER_ADMIN: [ROLE_USER, ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]

    providers:
        fos_userbundle:
            id: fos_user.user_provider.username
        in_memory:
            memory:
                users:
                    user:  { password: user, roles: [ 'ROLE_USER' ] }
                    admin: { password: admin, roles: [ 'ROLE_ADMIN' ] }

    firewalls:
        main:
            pattern: ^/
            form_login:
                #login_path: /
                provider: fos_userbundle
                csrf_provider: form.csrf_provider
            logout:       true
            anonymous:    true

    access_control:
        - { path: ^/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/register, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/resetting, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin, role: ROLE_ADMIN }
```        

Configure the FOSUserBundle

``` yaml
# app/config/config.yml

# FOSUser Configuration
fos_user:
    db_driver: orm
    firewall_name: main
    user_class: Twinpeaks\UserBundle\Entity\User
    group:
        group_class: Twinpeaks\UserBundle\Entity\UserGroup
```     

Add TPUserBundle to assetic
``` yaml
# app/config/config.yml
    bundles:        [ "TPUserBundle" ]
```     

Import routing files
``` yaml
# app/config/routing.yml
twinpeaks_user:
    resource: "@TPUserBundle/Controller/"
    type:     annotation
    prefix:   /  

fos_user_security:
    resource: "@FOSUserBundle/Resources/config/routing/security.xml"

fos_user_profile:
    resource: "@FOSUserBundle/Resources/config/routing/profile.xml"
    prefix: /profile

fos_user_register:
    resource: "@FOSUserBundle/Resources/config/routing/registration.xml"
    prefix: /register

fos_user_resetting:
    resource: "@FOSUserBundle/Resources/config/routing/resetting.xml"
    prefix: /resetting

fos_user_change_password:
    resource: "@FOSUserBundle/Resources/config/routing/change_password.xml"
    prefix: /profile
```

Update the schema database

``` php
#console
php app/console doctrine:schema:update --force
```

Create user
``` php
#console
php app/console fos:user:create
```

Promote user to SuperAdmin
Create user
``` php
#console
php app/console fos:user:promote username ROLE_SUPER_ADMIN
```
