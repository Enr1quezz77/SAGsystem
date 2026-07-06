# Cómo ejecutar tu proyecto con Docker

## Requisitos
- Docker instalado
- Docker Compose instalado
- (Opcional) Docker Desktop para interfaz gráfica

## Pasos para ejecutar

### 1. Construir e iniciar los contenedores
```bash
docker compose up --build
```

O en segundo plano:
```bash
docker compose up -d --build
```

### 2. Acceder a la aplicación
Abre en tu navegador:
```
http://localhost
```

### 3. Información de base de datos
- **Host**: `mysql` (desde dentro de Docker) o `localhost:3306` (desde tu máquina)
- **Usuario**: `admin`
- **Contraseña**: `admin_password`
- **Base de datos**: `sis_asistencia`
- **Root password**: `root_password`

### Comandos útiles

#### Ver logs de la aplicación
```bash
docker compose logs app
```

#### Ver logs de MySQL
```bash
docker compose logs mysql
```

#### Ejecutar comando en el contenedor PHP
```bash
docker compose exec app bash
```

#### Acceder a MySQL desde terminal
```bash
docker compose exec mysql mysql -u admin -p sis_asistencia
```

#### Detener los contenedores
```bash
docker compose down
```

#### Detener y eliminar todo (incluyendo datos)
```bash
docker compose down -v
```

## Estructura de archivos
- `Dockerfile` - Imagen PHP con Apache
- `docker-compose.yml` - Definición de servicios (PHP y MySQL)
- `.dockerignore` - Archivos a excluir de la imagen
- `.env` - Variables de entorno (configuración de BD)
- `config.php` - Actualizado para usar variables de entorno

## Notas
- Los datos de MySQL se persisten en un volumen llamado `mysql_data`
- Las carpetas `uploads` e `img` se montan como volúmenes para cambios en tiempo real
- El archivo `crear_bd_usuario.sql` se ejecuta automáticamente al crear el contenedor MySQL
