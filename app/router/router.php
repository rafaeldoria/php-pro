<?php
function routes()
{
    return require 'routes.php';
}

function exactMatchUriInArrayRoutes($uri, $routes)
{
    if(array_key_exists($uri, $routes)){
        return [$uri => $routes[$uri]];
    }
    return [];
}

function regualarExpressionMatchArrayRoutes($routes, $uri)
{
    return array_filter(
        $routes,
        function($value) use($uri){
            $regex = str_replace('/', '\/', ltrim($value, '/'));
            return preg_match("/^$regex$/", ltrim($uri, '/'));
        },
        ARRAY_FILTER_USE_KEY
    );
}

function params($matchedUri, $uri){
    if(!empty($matchedUri)){
        $matchedToGetParams = array_keys($matchedUri)[0];
        return array_diff(
            $uri,
            explode('/', ltrim($matchedToGetParams, '/')),
        );
    }
    return [];
}

function paramsFormat($params, $uri)
{
    $paramsData = [];
    foreach ($params as $key => $param) {
        $paramsData[$uri[$key - 1]] = $param;
    }
    return $paramsData;
}

function router()
{
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routes = routes();

    $matchedUri = exactMatchUriInArrayRoutes($uri, $routes);
    $params = [];
    if(empty($matchedUri)){
        $matchedUri = regualarExpressionMatchArrayRoutes($routes, $uri);
        $uri = explode('/', ltrim($uri, '/'));
        $params = params($matchedUri, $uri);
        $params = paramsFormat($params, $uri);
    }

    if(!empty($matchedUri)){
        return controller($matchedUri, $params);
    }

    throw new Exception('Something is wrong');
}