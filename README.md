# php-nv

# Requerimientos
* Install xammp
* Contar como PHP 8.0.13 en adelante
* Instalar composer en el equipo

# Instalción

En el directorio del proyecto ejecutar el siguiente comando de composer
```
composer require phpnv/api
```

Al ejecutar el comadno anterior se prodecera a instalar los paquetes de la libreria



# Creación del proyecto
Antes de iniciar debemos agregar los scripts del phpnv/api al composer.json del projecto.

Scrits
```JSON
"scripts":{
    "nv":"Phpnv\\Api\\Scripts\\Script::execute"
}
```

Ejecutamos el siguiente comando en la raiz del projecto
```
composer nv install
```

Importante actualizar el autoload de composer
```
composer dump-autoload
```


## Comandos de consola

* composer nv intall => Inicia el procesos de creación de los directorio y componentes necesarios para el funcionamiento de la api
* composer nv g p (name) => crea una nueva nueva rama de la api en caso de que se halla seleccionado el tipo multi api
* composer nv g c (name) (api name) => crea un controlador para asignar a una ruta. en caso de que sea multi api debe espificapar a cual api hace referencia.
* composer nv g m (name) (api name) => crea un modelo para el manejo de datos. en caso de que sea multi api debe espificapar a cual api hace referencia.
