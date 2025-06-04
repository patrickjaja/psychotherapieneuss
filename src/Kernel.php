<?php
namespace App;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\Loader\YamlFileLoader as RoutingYamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Kernel
{
    private ContainerBuilder $container;
    private RouteCollection $routes;

    public function __construct()
    {
        $this->container = new ContainerBuilder();
        $this->loadServices();
        $this->loadRoutes();
    }

    private function loadServices(): void
    {
        // Set parameters from environment
        $this->container->setParameter('kernel.project_dir', dirname(__DIR__));
        $this->container->setParameter('kernel.debug', $_ENV['APP_DEBUG'] === 'true');
        
        foreach ($_ENV as $key => $value) {
            $this->container->setParameter('env(' . $key . ')', $value);
        }
        
        $loader = new YamlFileLoader($this->container, new FileLocator(__DIR__ . '/../config'));
        $loader->load('services.yaml');
        $this->container->compile();
    }

    private function loadRoutes(): void
    {
        $fileLocator = new FileLocator(__DIR__ . '/../config');
        $loader = new RoutingYamlFileLoader($fileLocator);
        $this->routes = $loader->load('routes.yaml');
    }

    public function handle(Request $request): Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $parameters = $matcher->match($request->getPathInfo());
            $request->attributes->add($parameters);
            
            // Get controller class and method
            list($controllerClass, $method) = explode('::', $parameters['_controller']);
            
            // Create controller instance with dependencies
            if ($this->container->has($controllerClass)) {
                $controller = $this->container->get($controllerClass);
            } else {
                // Special handling for ContactController
                if ($controllerClass === 'App\Controller\ContactController') {
                    $controller = new $controllerClass(
                        $this->container->get('Twig\Environment'),
                        $this->container->get('App\Service\DatabaseService'),
                        $this->container->get('App\Service\MailerService')
                    );
                } else {
                    // Default for other controllers
                    $controller = new $controllerClass($this->container->get('Twig\Environment'));
                }
            }
            
            // Call the method
            $response = $controller->$method($request);
            
            if (!$response instanceof Response) {
                throw new \LogicException('The controller must return a Response object');
            }
            
            return $response;
        } catch (\Exception $e) {
            return new Response('Ein Fehler ist aufgetreten: ' . $e->getMessage(), 500);
        }
    }
}