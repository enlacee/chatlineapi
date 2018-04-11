# CHATLINEAPI

with Slim Framework 3

## Manual de Desarrollo
Crear servidor para pruebas

	php -S localhost:8000 

### Generate test data

	localhost:8080/v1/faker-data

## Iniciar Socket 
Ejecutar el comando en la terminal bash

	php public/server.php start

## Configurar Apache
En el puerto :8080 configuración de PHP en el archivo `php.ini`

	Wampserver > PHP > php.ini

	#Listen 12.34.56.78:80
	Listen 0.0.0.0:8080
	Listen [::0]:8080

Configuración **wampserver** aplicación
Abrir archivo: `C:\wamp\wampmanager.ini` y agregar el puerto  
buscar en este texto : ;WAMPMENULEFTSTART y agregar el puero nuevo 

#### #Paso 01: Fue necesario ya que al subir imagen no lee el MIDLEWARE (slimframework) que respondia con el soporte CORS, metodo por htaccess.

Configurar el archivo `public/.htaccess` para sopote CORS
puede ser un metodo inseguro por dejar abierto a todos el acceso.

	<IfModule mod_rewrite.c>
	  Header add Access-Control-Allow-Origin "*"
	  Header add Access-Control-Allow-Methods: "GET, POST, PUT, DELETE, PATCH, OPTIONS"
	  Header add Access-Control-Allow-Headers: "Content-Type"
	  
	  RewriteEngine On
	  RewriteCond %{REQUEST_URI}::$1 ^(/.+)/(.*)::\2$
	  RewriteRule ^(.*) - [E=BASE:%1]
	  
	  # If the above doesn't work you might need to set the `RewriteBase` directive manually, it should be the
	  # absolute physical path to the directory that contains this htaccess file.
	  # RewriteBase /

	  RewriteCond %{REQUEST_FILENAME} !-f
	  RewriteRule ^ index.php [QSA,L]
	</IfModule>

#### #Paso 02: Activar el modulo `Headers` de apache
en windows (buscar headers)

	sudo a2enmod headers

Paso ultimo reiniciar apache

## Configurar base de datos

#### #Paso 01: Generar la base de datos

El modelo de base de datos se encuentra en `src\database.mwb` instalar la apliación si aún no lo tienes [descargar workbench](https://www.mysql.com/products/workbench/)
Abrir y generar el modelo de base de datos llamado **chatline**, dentro de la aplicación

	Database > Forward Enginneer

#### #Paso 02: Configurar

La configuración de la base de datos se encuentra en `src/settings.php` y cambiar los valores
cambiar a los valores según la configuracion interna o agregar la configuración por defecto **root**

		'database' => [
			'host' => 'localhost',
			'user' => 'root',
			'pass' => '',
			'dbname' => 'chatline',
		],

## Configurar el envio de correos (en windows)

Referencia [video enviar correos electronicos](https://www.youtube.com/watch?v=fiUKU3e1EJ4) pasos a seguir:

#### #Paso 01

Referencia base [send mail from localhost/WAMP](http://blog.techwheels.net/send-email-from-localhost-wamp-server-using-sendmail/)

Descargar el archivo [zip sendmail](http://www.glob.com.au/sendmail/sendmail.zip) carpeta \sendmail y extraer dentro de `c:\wamp` y editar el archivo `sendmail.ini`
En la linea cambiar a GMAIL

	[sendmail]
	smtp_server=smtp.gmail.com
	smtp_port=465
	.
	.
	.
	auth_username=acopitan.xxx@gmail.com
	auth_password=clavefacil#xxx

#### #Paso 02

Configurar en wampserver abrir `Wampserver > PHP > php.ini`
buscar: sendmail_path  

	sendmail_path = C:\wamp\sendmail\sendmail.exe -t

También cambiar la configuracion del SMTP, cambiar el puerto

		[mail function]
	smtp_port = 465

