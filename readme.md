# Menu manager

## Getting started
Copy or Rename .env.example to create the environment. 

Head over to https://vessel.shippingdocker.com/ for instructions on using application with docker.

## Time-up.. With more time i would have:
* Completed the remaining end-points
* Looked into caching and a better way of getting the total depth.
* Looked into caching (not only caching stuff but removing of the cache keys which i find harder and more important than caching itself)
* I would also have looked into transforming the input/output for protection of our database fields

## Considerations

I am heavily inspired by making less-code, and utilizing every aspect of open-source which is why i have chosen to use a package for most of the work `https://github.com/lazychaser/laravel-nestedset`.    

As dealing with nested sets of data can be quite challenging if not done right.

By using this package i open myself up to the possibility of moving the data around. E.g move the Menu Items between parents. Finding the depth.  

If I chose to do this by myself i would have to make a lot of research regarding the `https://en.wikipedia.org/wiki/Nested_set_model`.

## Endpoints Done
```
+--------+-----------+---------------------------------+-------------------+-----------------------------------------------------+------------+
|        | POST      | api/items                       | items.store       | App\Http\Controllers\ItemController@store           | api        |
|        | DELETE    | api/items/{item}                | items.destroy     | App\Http\Controllers\ItemController@destroy         | api        |
|        | PUT|PATCH | api/items/{item}                | items.update      | App\Http\Controllers\ItemController@update          | api        |
|        | GET|HEAD  | api/items/{item}                | items.show        | App\Http\Controllers\ItemController@show            | api        |
|        | DELETE    | api/items/{item}/children       |                   | App\Http\Controllers\ItemChildrenController@destroy | api        |
|        | GET|HEAD  | api/items/{item}/children       |                   | App\Http\Controllers\ItemChildrenController@show    | api        |
|        | POST      | api/items/{item}/children       |                   | App\Http\Controllers\ItemChildrenController@store   | api        |
|        | GET|HEAD  | api/menus                       | menus.index       | App\Http\Controllers\MenuController@index           | api        |
|        | POST      | api/menus                       | menus.store       | App\Http\Controllers\MenuController@store           | api        |
|        | DELETE    | api/menus/{menu}                | menus.destroy     | App\Http\Controllers\MenuController@destroy         | api        |
|        | PUT|PATCH | api/menus/{menu}                | menus.update      | App\Http\Controllers\MenuController@update          | api        |
|        | GET|HEAD  | api/menus/{menu}                | menus.show        | App\Http\Controllers\MenuController@show            | api        |
|        | DELETE    | api/menus/{menu}/items          |                   | App\Http\Controllers\MenuItemController@destroy     | api        |
|        | POST      | api/menus/{menu}/items          | menus.items.store | App\Http\Controllers\MenuItemController@store       | api        |
|        | GET|HEAD  | api/menus/{menu}/items          | menus.items.index | App\Http\Controllers\MenuItemController@index       | api        |
|        | GET|HEAD  | api/menus/{menu}/depth          | menus.items.index | App\Http\Controllers\MenuDepthController@invoke       | api        |
+--------+-----------+---------------------------------+-------------------+-----------------------------------------------------+------------+
```

## Sources used
* https://en.wikipedia.org/wiki/Nested_set_model
* https://github.com/lazychaser/laravel-nestedset
