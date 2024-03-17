
# README

Este es el archivo README para el proyecto. Aquí encontrarás los pasos necesarios para instalar y configurar el proyecto en tu entorno local.

## Pasos para instalar el proyecto

1. Clona el repositorio utilizando Git.

2. Descarga e instala la herramienta XAMPP desde [apachefriends.org](https://www.apachefriends.org/es/index.html).

3. Mueve el repositorio clonado a la siguiente ubicación en tu sistema: `C:\xampp\htdocs`.

4. Inicia el panel de control de XAMPP y asegúrate de iniciar los servicios Apache y MySQL.

5. Abre tu IDE (por ejemplo, VSCode) y accede a la carpeta del repositorio clonado.

6. Ejecuta el comando `composer install` en la terminal para instalar todas las dependencias del proyecto.

7. Para iniciar el servidor, ejecuta el siguiente comando en la terminal:

    ```
    php -S 127.0.0.1:8000 -t public
    ```

8. Para migrar la base de datos, ejecuta los siguientes comandos en la terminal:

    ```
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
    ```

9. Accede a la base de datos utilizando phpMyAdmin desde [localhost/phpmyadmin](http://localhost/phpmyadmin/index.php?route=/server/databases).

10. Debería haberse creado la base de datos llamada "APP" con las tablas correspondientes.

11. Para ejecutar las pruebas unitarias, utiliza el siguiente comando en la terminal:

    ```
    php bin/phpunit
    ```

¡Listo! Ahora el proyecto debería estar instalado y configurado correctamente en tu entorno local.
