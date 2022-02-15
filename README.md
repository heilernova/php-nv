# php-nv

# Requerimientos
* Install xammp
* Contar como PHP 8.0.13 en adelante
* Instalar composer en el equipo

# Recomendaciones
Trabajar namespace en las clases, importante que el namespace con concuerde con ruta donde esta alojada la clase
para que el autoload de composer la pueda utilizar.

# Instalción

En el directorio del proyecto ejecutar el siguiente comando de composer
```
composer require phpnv/api
```

# Creación del proyecto
Antes de iniciar debemos agregar los scripts del phpnv/api al composer.json del projecto.

Scrits
```JSON
"scripts":{
    "nv":"Phpnv\\Api\\Scripts\\Script::execute"
}
```
## Pasos para la creación del entorno de trabajo
* Paso 1
Ejecutamos el siguiente comando en la raíz del projecto
```
composer nv install
```
Una ves ejecutado el script preguntara si se ejecutara varias ramas de la api o solo una.
Una vez escojido no se podra modificar el desarrollo del proyecto.
Importante actualizar el autoload de composer
```
composer dump-autoload
```

## Definición de rutas de acceso

Las rutas de deben de finicir en el archivo api-routes.php, en caso de utilizar multi api cada directorio tendra un archivo similar que inia con el nombre ejm. [ name-routes.php ]

Definir una ruta que ejecuta una función.
```PHP
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', function(){ return new Response('Hola mundo'); });
```

Definir un ruta que ejecuta un controlador.
```php
namespace Api\Http\Controllers;

use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', [TestController::class, 'get']);
```
## Protección de rutas
Para protejer la rutas y evitar el acceso a cualquir dispositivo utilizaremos la clase guard que se genera en cada api, el archivo lo encontraremos en el direcotrio Http con el nombre Guard.php

El guard es una clase con métodos estaticos que retornan un callable para ser ejecutadas en la rutas antes de ralizar la acción
si callable retorna null se dara acceso a la ruta, en caso contrario retornara un objeto Response.

Ejemplo del método autenticado del Guard. todos las rutas que lo utilece solo daran acceso a la ruta el random_int es igual a 1.
```php
<?php
namespace Api\Http;

use Phpnv\Api\Response;

class Guard
{
    public static function autenticate():callable
    {
        return function(){
            if (random_int(1,2) == 1){
                return null; // Accesso permitido
            }else{
                // Denega el acceso y reponse no access.
                return new Response('No access - random_int no es 1',  401);
            }
        };
    }
}
```

Implementación del guard en la ruta
```php
namespace Api\Http\Controllers;

use Api\Http\Guard;
use Phpnv\Api\Response;
use Phpnv\Api\Routes\Routes;

Routes::get('test', [TestController::class, 'get'], [Guard::autenticates()]);
```


## Comandos de consola

* composer nv intall => Inicia el procesos de creación de los directorio y componentes necesarios para el funcionamiento de la api
* composer nv g p (name) => crea una nueva nueva rama de la api en caso de que se halla seleccionado el tipo multi api
* composer nv g c (name) (api name) => crea un controlador para asignar a una ruta. en caso de que sea multi api debe espificapar a cual api hace referencia.
* composer nv g m (name) (api name) => crea un modelo para el manejo de datos. en caso de que sea multi api debe espificapar a cual api hace referencia.
