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
        new Twinpeaks\UserBundle\TPUserBundle(),
    );
}
```

Configure your application's security.yml

``` yaml
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
