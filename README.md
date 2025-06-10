# eRPair

¡Bienvenido a **eRPair**!  
Una plataforma innovadora para la gestión eficiente de reparaciones electrónicas.
---

## 📋 Requisitos

- PHP >= 8.2
- Composer
- MySQL o MariaDB
- Conexión a internet
- Navegador

---
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
## MODULOS

## 💵 Cajas (cashDesk)

El módulo de cajas permite llevar un control detallado de los movimientos de efectivo dentro del sistema.  

- Cerrar cajas diarias.
- Registrar ingresos totales de efectivo y tarjeta.
- Cálculo automático de ingresos totales.

Este módulo facilita la gestión transparente y segura del flujo de caja en tu negocio.
---
## 👥 Clientes
El módulo de clientes permite gestionar la información de tus clientes de manera eficiente.

- Registrar nuevos clientes.
- Editar la información de clientes existentes.
- Consultar la lista de dispositivos asociados a cada cliente.

---
## 👤 Usuarios/Mi Usuario

El módulo de usuarios permite gestionar la información de los usuarios del sistema.

- Registrar nuevos usuarios.
- Editar la información de usuarios existentes.
- Consultar o editar la lista de roles o tiendas asociados a cada usuario.

En caso de no ser administrador, el usuario podrá editar únicamente su propia información.
---

## 📝 Hoja de Pedidos

El módulo de **Hoja de Pedidos** te ayuda a gestionar fácilmente todas las órdenes de trabajo.

- Edita pedidos existentes antes de 30 minutos.
- Consulta el estado.
- Facturas asociadas.
- Visualiza los estados, el cierre y los ítems incluidos en cada pedido.
- 👷‍♂️ Si eres **técnico**, puedes: 
   - Asignarte pedidos para gestionarlos.
    - Cambiar el estado del pedido a "Pendiente de Pieza" si es necesario.
    - Realizar un cierre (reparado)
- 🧑‍💼 Si eres **dependiente/encargado/admin**  puedes:
    - Realizar cobros (anticipados o finales).
    - Agregar Devoluciones.
    - Agregar Garantías.
    - Cambiar el estado del pedido:
        - Cancelarlo (común).
        - Una vez facturado y cerrado, pasa a "Facturado" y "Entregado".
---
## 🏷️ Marcas

El módulo de marcas te permite gestionar las marcas de los dispositivos de manera eficiente.

- Registrar nuevas marcas.
- Editar la información de marcas existentes.
- Consultar la lista de modelos asociados a cada marca.
- Asociar y editar la lista de modelos vinculados a cada marca.
- Acceder a un modelo, editar los ítems asociados y modificar el stock en las distintas tiendas.Marcas
---
## 📱 Dispositivos
El módulo de dispositivos te permite gestionar los dispositivos de manera eficiente.

- Consultar la lista de dispositivos.
- Acceder a los pedidos asociados a cada dispositivo.

---
## 🛠️ Ítems
El módulo de ítems te permite gestionar los ítems de manera eficiente.
- Consultar, agregar y editar la lista de ítems.

---
## 📂 Miscelanea

El sistema incluye varias tablas tipo que permiten únicamente agregar o listar registros, facilitando la gestión de información estructurada:
- 🗂️ **Categorías:** Organiza los dispositivos, ítems o servicios en diferentes categorías para una mejor clasificación.
- ⏱️ **Tiempos de reparación:** Define y consulta los tiempos estándar de reparación para distintos tipos de trabajos.
- 💸 **Impuestos:** Gestiona los diferentes tipos de impuestos aplicables a las facturas.
- 🏷️ **Tipo de ítems:** Clasifica los ítems según su naturaleza (por ejemplo, repuesto, accesorio, servicio, etc.).

Estas tablas ayudan a mantener la información organizada y estandarizada en la plataforma.
---
## 🏢 Empresas
El módulo de empresas permite gestionar la información de las empresas asociadas al sistema.
- Registrar nuevas empresas.
- Editar la información de empresas existentes.

---
## 🧾 Facturas
El módulo de facturas te permite gestionar las facturas de manera eficiente.
- Consultar la lista de facturas.
- Permite solo editar método o empresa asociada a la factura.
- Exportar facturas a PDF.

---
## 🧾 Datos Fiscales
El módulo de datos fiscales permite gestionar la información fiscal de tu empresa.  
- Registrar los datos fiscales de tu empresa.
- Editar la información fiscal existente.
- Consultar la información fiscal asociada a las facturas.
---
## 🏬 Tiendas
El módulo de tiendas permite gestionar la información de las tiendas asociadas al sistema.
- Registrar nuevas tiendas.
- Editar la información de tiendas existentes.
---

## 📖 Manual de Usuario

A continuación, se describen los pasos básicos para utilizar la plataforma **eRPair**:

### 1. Iniciar sesión
Accede con tus credenciales proporcionadas por el administrador.

### 2. Navegación principal
Utiliza el menú lateral para acceder a los diferentes módulos: Pedidos, Clientes, Facturas, Cajas, etc.

### 3. Crear una orden de trabajo
- Dirígete al módulo **Hoja de Pedidos**.
- Haz clic en "Nuevo Pedido".
- Completa los datos requeridos y guarda.

### 4. Gestionar clientes
- Accede al módulo **Clientes**.
- Añade, edita o consulta información de clientes y sus dispositivos.

### 5. Facturación
- En el módulo **Facturas**, consulta, edita o exporta facturas.
- Asocia facturas a pedidos y empresas.

### 6. Cierre de caja
- Ve al módulo **Cajas** para registrar ingresos y cerrar la caja diaria.

### 7. Configuración
- Ajusta datos fiscales, tiendas, empresas y usuarios desde sus respectivos módulos.

Para más detalles, consulta la documentación interna o contacta con el administrador del sistema.

Desarrollado por [Ismael Rodriguez Cuenca](https://github.com/ismaelrodcuenca)  

