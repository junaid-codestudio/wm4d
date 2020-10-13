<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Sqs
{
  /**
  * Handle an incoming request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @param  \Closure  $next
  * @return mixed
  */
  public function handle(Request $request, Closure $next)
  {
    $path = \Route::getCurrentRoute()->getName();
    $currentAction = \Route::currentRouteAction();
    list($controller, $method) = explode('@', $currentAction);

    $controller = preg_replace('/.*\\\/', '', $controller);

    $app = app();
    $controller = $app->make('\App\Http\Controllers\\'. $controller );

    return $controller->callAction($path, $parameters = array());

    // return $next($request);
  }
}
