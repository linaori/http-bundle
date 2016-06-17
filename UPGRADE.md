
1.1.0
-----

 * The `EntityIdResolver` is marked as deprecated and will be removed in 2.0.
 * The service `iltar_http.router.entity_id_resolver` is now using the new
   `Iltar\HttpBundle\Router\Resolver\IdentifyingValueResolver` which has the 
   same functionality. 
 * The `Iltar\HttpBundle\Router\MappablePropertyPathResolver` is marked 
   deprecated and the 
   `Iltar\HttpBundle\Router\Resolver\MappablePropertyPathResolver` should
   be used instead

1.0.0
-----

 * Initial release.
