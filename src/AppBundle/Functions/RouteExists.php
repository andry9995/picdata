<?php
/**
 * Created by PhpStorm.
 * User: TEFY
 * Date: 06/04/2017
 * Time: 09:53
 */

namespace AppBundle\Functions;


use Symfony\Component\Routing\Router;

class RouteExists extends \Twig_Extension
{
    private $router;

    public function __construct(Router $route)
    {
        $this->router = $route;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('route_exists', array($this, 'routeExistsFilter')),
        );
    }

    public function routeExistsFilter($route)
    {
        return (null === $this->router->getRouteCollection()->get($route)) ? false : true;
    }
}