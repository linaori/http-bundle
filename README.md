http-bundle
===========
Provides extra HTTP related functionality in Symfony.

Requirements:
 - PHP 5.5 or higher, including php 7
 - Symfony 2.7 or higher

Recommended installation is via composer: `composer require iltar/http-bundle`.

Router Enhancements
-------------------

```php
    /**
     * @Route("/profile/{user}/", name="app.view-profile")
     */
    public function viewProfileAction(AppUser $user);
```

Let's say we have a [ParamConverter](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html)
on this route that would nicely convert the `user` parameter of your route to an `AppUser`, but how would you
generate the route for this action? Sadly you still need a scalar as parameter for `Router::generate()`:

```php
$router->generate('app.view-profile', ['user' => $user->getId()]);
```

Okay, not really a big problem, but we're passing an id to a parameter which you would expect
to have an `AppUser` if you look at the action. Another problem is that if you want to change
the argument passed along, you will have to update every single usage of this URL. A decent
IDE can get around this issue, but wait! What about your twig templates?

```twig
{{ path('app.view-profile', { 'user': user.id }) }}
{{ path('app.view-profile', { 'user': user.getid }) }}
{{ path('app.view-profile', { 'user': user.getId() }) }}
{{ path('app.view-profile', { 'user': user[id] }) }}

{# I think you see where I'm going at #}
```

How nice would it be if you could just pass your user object along?

```php
$router->generate('app.view-profile', ['user' => $user]);
```

> The router decorator is provided to make it easier to resolve arguments
  required for route generation. They work like a reversed
  [ParamConverter](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html).

#### A quick Example
You need to do two things to make this work:
 - create a resolver
 - write a service definition for it and tag it accordingly

```php
<?php
namespace App\Router;

use App\AppUser;
use Iltar\HttpBundle\Router\ParameterResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class UserParameterResolver implements ParameterResolverInterface
{
    public function supportsParameter($name, $value)
    {
        return 'user' === $name && $value instanceof AppUser
    }

    /**
     * Resolves AppUser for 'user' to AppUser::getUsername().
     *
     * {@inheritdoc}
     */
    public function resolveParameter($name, $value)
    {
        return $value->getUsername();
    }
}

```

And don't forget the service definition.
```yml
services:
    app.router.user-param-resolver:
        class: App\AppUser\UserParameterResolver
        tags:
            - { name: router.parameter_resolver, priority: 150 }
```

#### That's too much work!

This is very nice, but a lot of work. Do I really have to write a resolver for each parameter
I want to have resolved? The answer is simple: no. This package comes with two resolvers
already.

The first resolver is the `IdentifyingValueResolver`. This resolver functions as a delegator
for models implementing the `ModelDescriptorInterface`. This resolver will attempt to find
the identifying value and obtain it. Currently this package ships with the following descriptors:
 - `EntityIdDescriptor` - Attempts to get the value marked with `@Id` from a doctrine entity.


The second resolver provided is the `MappablePropertyPathResolver`. It does two things:
 - Automagically tries to resolve the property required (more on this later),
   using Symfony's [Property Accessor](https://github.com/symfony/PropertyAccess)
   component.
 - Allows you to override or wildcard certain objects via configuration.

The `IdentifyingValueResolver` with the `EntityIdDescriptor` is registered with a priority of 
100 by default. The `MappablePropertyPathResolver` is registered with a priority of 200.

#### So how can I make it resolve my properties?

It follows three checks:
 - Is the object provided mapped in the configuration?
 - Is the name of the argument mapped in the configuration?
 - Is the name of the argument an accessible property in the object?

```php
// let's take this simple example
$router->generate('app.view-user', ['username' => $user]);
$router->generate('app.view-user', ['id' => $user]);
```

Without any configuration, the first would attempt to call `$user->getUsername()` and the
second `$user->getId()`.

```php
// let's take another example
$router->generate('app.view-user', ['user' => $user]);
```

##### Aliasing a property

This serves a bit more problematic, as there is no user property within the `AppUser`. You can
work around this issue by defining the mapping in the configuration. This configuration example
would call  `$user->getUsername()` if the key `user` and the object an `AppUser`.

```yml
iltar_http:
    router:
        mapped_properties:
            App\AppUser.user: username
```

##### Wildcards

But my application is a bit messy and I have several different ways of getting the `id` from a user:
`user_id`, `user`, `id` and `uid`, how do I solve this without writing a lot of configuration?
Solution: use the wildcard declaration. The wildcard declaration will make sure that if the `AppUser`
is being passed along, it will always get the `id`.

```yml
iltar_http:
    router:
        mapped_properties:
            App\AppUser: id
```

But this makes it problematic when I still want to be able to get the `username` on some pages but the
`id` on the rest, how do I solve this? There's a solution for that as well! Next to a wildcard, you can
also specify the `username`. If a more specific field is configured, it will take that over the wildcard
if the name of the parameter matches.

```yml
iltar_http:
    router:
        mapped_properties:
            App\AppUser          : id
            App\AppUser.user     : username
            App\AppUser.username : ~
```

The first line tells that all undefined keys match the `id`. The second line tells that if they key is
`user`, it should get the `username`. The third line means that it should pick the parameter name as 
property name.


##### Power of the Property Accessor Component

I'm lazy though, what if I want link to the first address of my Client, which has a one to many relation?

```yml
iltar_http:
    router:
        mapped_properties:
            App\Client.first_address: address[0]
```

##### Using the IdentifyingValueResolver Alongside the MappablePropertyPathResolver

If you enable the `IdentifyingValueResolver`, you can already cover the cases where your parameter is the `id`.
When enabled you can can remove the definitions of the `mapped_properties` where you would refer to the
primary key (usually id). The identifier is not limited to `id` or `getId()`, the resolver will dynamically
attempt to find which field is the primary key. If the id happens to be another entity, it will try to get
the id of that given entity until it finds a scalar. It does not support composite primary keys.

To enable the id resolver you have to enable the configuration:
```yml
iltar_http:
    router:
        entity_id_resolver: true
```
