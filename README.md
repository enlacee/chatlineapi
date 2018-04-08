# CHATLINEAPI

with Slim Framework 3


## SLIM DOC
Crear servidor de prueba

	php -S localhost:8000

## Generate test data

	local.chatlineapi.com/v1/faker-data

## start socket 

	php public/server.php start


### Configurar Apache

Configurar archivo ´public/.htaccess´
Agregar las siguientes lineas, para soportar CORS es importante agregar lo siguiente:
puede ser un metodo inseguro por dejar abierto a todos el acceso.

01: Fue necesario ya que al subir imagen no leia el MIDLEWARE que respondia con el soporte CORS, metodo por htaccess

	Header set Access-Control-Allow-Origin "*"


	<IfModule mod_rewrite.c>
	  Header add Access-Control-Allow-Origin "*"
	  Header add Access-Control-Allow-Methods: "GET, POST, PUT, DELETE, PATCH, OPTIONS"
	  Header add Access-Control-Allow-Headers: "Content-Type"
	  
	  RewriteEngine On

	  # Some hosts may require you to use the `RewriteBase` directive.
	  # Determine the RewriteBase automatically and set it as environment variable.
	  # If you are using Apache aliases to do mass virtual hosting or installed the
	  # project in a subdirectory, the base path will be prepended to allow proper
	  # resolution of the index.php file and to redirect to the correct URI. It will
	  # work in environments without path prefix as well, providing a safe, one-size
	  # fits all solution. But as you do not need it in this case, you can comment
	  # the following 2 lines to eliminate the overhead.
	  RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
	  RewriteRule ^(.*) - [E=BASE:%1]
	  
	  # If the above doesn't work you might need to set the `RewriteBase` directive manually, it should be the
	  # absolute physical path to the directory that contains this htaccess file.
	  # RewriteBase /

	  RewriteCond %{REQUEST_FILENAME} !-f
	  RewriteRule ^ index.php [QSA,L]
	</IfModule>

02: Activar el modulo ´Headers´ de apache
en windows (buscar headers)

	sudo a2enmod headers

03: reiniciar apache