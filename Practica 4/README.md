# COMANDO PARA INICIAR EL SERVIDOR
## Ejecutar el comando en el directorio donde se encuentra toda la estructura de archivos
php -S localhost:8000 -t public/


# PÁGINA DE FLIGHT CONSTRUIR BLOG
[flight.php](https://docs.flightphp.com/es/v3/guides/blog)

# ENUNCIADO DE LA PRÁCTICA

Realizar una aplicación web con Flight PHP y Latte que permita buscar e insertar información en un archivo .json

Este archivo debe contener inicialmente la información del array tridimensional asociativo y escalar que utilizaste en la práctica UD 2.

La aplicación web debe cumplir las siguientes especificaciones:

    • Utilizar sesiones para registrar el usuario que interactúa con la aplicación y visualizar su nombre en el dashboard.

    • En la página principal se mostrará un formulario para búsquedas por cada una de las claves. Los resultados de las búsquedas se mostrarán en la página principal.

    • En la página principal se incluirá también un enlace al dashboard. 

En el dashboard se mostrará el nombre del usuario y un formulario para la inserción de un nuevo objeto en el archivo .json

Antes de llevar  la inserción se deberá verificar:

    • que los datos no son NULL
    • que no hay valores duplicados 
    • que la longitud de los datos es la adecuada 
    • que no hay caracteres numéricos en cadenas de caracteres cuando no procede

     En caso de que una o más de estas verificaciones no se cumplan se visualizarán los mensajes de errores que correspondan y se repintará el formulario. 

Realizada la inserción el código redirigirá a la página principal. 

# EVALUACIÓN DE LA PRÁCTICA

- Se han analizado y utilizado mecanismos y frameworks que permiten realizar esta separación y sus características principales. 

- Se han utilizado objetos y controles en el servidor para generar el aspecto visual de la aplicación web en el cliente. 

- Se han utilizado formularios generados de forma dinámica para responder a los eventos de la aplicación web. 

- Se han identificado y aplicado los parámetros relativos a la configuración de la aplicación web. 

- Se han escrito aplicaciones web con mantenimiento de estado y separación de la lógica de negocio. 

- Se han aplicado los principios y patrones de diseño de la programación orientada a objetos. 

- Se ha probado y documentado el código. 

- Se han identificado las tecnologías y frameworks relacionadas con la generación por parte del servidor de páginas web con guiones embebidos. 

- Se han utilizado estas tecnologías y frameworks para generar páginas web que incluyan interacción con el usuario. 

- Se han utilizado estas tecnologías y frameworks, para generar páginas web que incluyan verificación de formularios. 

- Se han utilizado estas tecnologías y frameworks para generar páginas web que incluyan modificación dinámica de su contenido y su estructura.