# eRPair

¡Bienvenido a **eRPair**!  
Una plataforma innovadora para la gestión eficiente de reparaciones electrónicas.

---

## 🚀 Características

- Gestión ordenes de trabajo
- Seguimiento del estado de la reparacion de los dispotivos
- Base de datos de marcas, modelos, items, etc
- Gestion de facturacion.
- Generacion de total facturado
- Otros...

---

## 🛠️ Instalación

1. Clona el repositorio:
    ```bash
    git clone https://github.com/tu-usuario/eRPair.git
    ```
2. Instala las dependencias:
    ```bash
    composer i 
    ```
3. Inicia la aplicación:
    ```bash
    cp .env.example .env 
    ```
4. Genera la clave de la aplicación:
    ```bash
    php artisan key:generate
    ```
5. Configura la contraseña de tu usuario de MySQL en el archivo `.env`:
    ```
    DB_PASSWORD=tu_contraseña_mysql
    ```
6. Ejecuta las migraciones y los seeders:
    ```bash
    php artisan migrate:fresh --seed
    ```
7. Inicia el servidor de desarrollo:
    ```bash
    php artisan serve
    ```
---

**Desarrollado por Ismael Rodriguez Cuenca**