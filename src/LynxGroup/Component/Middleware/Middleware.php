<?php namespace LynxGroup\Component\Middleware;

use LynxGroup\Contracts\Middleware\MiddlewareInterface;

use Psr\Http\Message\RequestInterface;

use Psr\Http\Message\ResponseInterface;

class Middleware implements MiddlewareInterface
{
	protected $middlewares = [];

	protected $resolver;

	public function add($middleware)
	{
		$this->middlewares[] = $middleware;
	}

	public function setResolver($resolver)
	{
		$this->resolver = $resolver;
	}

	public function __invoke($request, $response)
	{
		$entry = array_shift($this->middlewares);
 
		$middleware = $this->resolve($entry);

		return $middleware($request, $response, $this);
	}

	public function resolve($entry)
	{
		if( !$entry )
		{
			return function (RequestInterface $request, ResponseInterface $response, callable $next)
			{
				return $response;
			};
		}

		return call_user_func($this->resolver, $entry);
	}
}
