# Sorteo de bancas PPC

Aplicacion PHP para procesar el padron y realizar el sorteo de bancas PPC.

## Desplegar con Docker

Clonar el proyecto en el servidor y entrar al directorio:

```bash
git clone https://github.com/hcdposadas/sorteo-bancas.git
cd sorteo-bancas
```

Construir la imagen:

```bash
docker build -t sorteo-bancas .
```

Como el proyecto `sorteo-ppc-escuelas` ya utiliza el puerto local `8080`,
publicar este contenedor en el puerto local `8081`:

```bash
docker run -d \
  --name sorteo-bancas \
  --restart unless-stopped \
  -p 127.0.0.1:8081:80 \
  sorteo-bancas
```

La aplicacion quedara disponible en:

```text
http://138.117.79.254/sorteo-ppc/
```

## Configurar Nginx en el servidor

No crear otro bloque `server` con el mismo puerto y `server_name`. Editar el
archivo existente:

```bash
sudo nano /etc/nginx/sites-available/sorteo-ppc
```

Agregar las ubicaciones de `/sorteo-ppc` al mismo bloque que ya publica
`/sorteo-ppc-escuelas`:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name 138.117.79.254;

    client_max_body_size 10m;

    location = /sorteo-ppc-escuelas {
        return 301 /sorteo-ppc-escuelas/;
    }

    location /sorteo-ppc-escuelas/ {
        proxy_pass http://127.0.0.1:8080;

        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        proxy_read_timeout 120s;
    }

    location = /sorteo-ppc {
        return 301 /sorteo-ppc/;
    }

    location /sorteo-ppc/ {
        proxy_pass http://127.0.0.1:8081;

        proxy_http_version 1.1;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;

        proxy_read_timeout 120s;
    }
}
```

Validar la configuracion y recargar Nginx:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Actualizar el despliegue

```bash
cd sorteo-bancas
git pull
docker build -t sorteo-bancas .
docker stop sorteo-bancas
docker rm sorteo-bancas
docker run -d \
  --name sorteo-bancas \
  --restart unless-stopped \
  -p 127.0.0.1:8081:80 \
  sorteo-bancas
```

## Verificar y administrar

```bash
docker ps --filter name=sorteo-bancas
docker logs --tail 100 sorteo-bancas
curl -I http://138.117.79.254/sorteo-ppc/
```

Reiniciar o detener la aplicacion:

```bash
docker restart sorteo-bancas
docker stop sorteo-bancas
```
