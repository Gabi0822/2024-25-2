<?php

 namespace App\Http\Middleware;

 use Closure;
 use Illuminate\Http\Request;
 use Symfony\Component\HttpFoundation\Response;

 class ForceJSONResponseAPIMiddleware
 {
     /**
      * Handle an incoming request.
      *
      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
      */
     public function handle(Request $request, Closure $next): Response
     {
         if($request->is('api/*') && !$request->headers->contains('Accept', 'application/json')){
             $request->headers->set('Accept', 'application/json' );
         }

         if($request->is('api/*') && !$request->headers->contains('Content-type', 'application/json')){
             $request->headers->set('Content-Type', 'application/json' );
         }
         return $next($request);
     }
 }
