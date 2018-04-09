# CHATLINEAPI

with Slim Framework 3


## SLIM DOC
Crear servidor de prueba

	php -S localhost:8000

## Generate test data

	localhost:8080/v1/faker-data

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

03: Reiniciar apache

#### configurar en apache el puerto :8080

	Wampserver > PHP > php.ini

	#Listen 12.34.56.78:80
	Listen 0.0.0.0:8080
	Listen [::0]:8080

Configuracion wampserver servicio
Abrir archivo: ´C:\wamp\wampmanager.ini´ y agregar el puerto
buscar en este texto : ;WAMPMENULEFTSTART y agregar el puero nuevo

#### configurar el envio de correos

Referencia [video enviar correos electronicos](https://www.youtube.com/watch?v=fiUKU3e1EJ4)
Pasos a seguir:

###### #A

Referencia base [send mail from localhost/WAMP](http://blog.techwheels.net/send-email-from-localhost-wamp-server-using-sendmail/)

Descargar el archivo [zip sendmail](http://www.glob.com.au/sendmail/sendmail.zip) carpeta \sendmail y extraen dentro de c:\wamp
Editar el archivo ´sendmail.ini´
En la linea cambiar a GMAIL

	[sendmail]
	smtp_server=smtp.gmail.com
	smtp_port=465
	.
	.
	.
	auth_username=acopitan.xxx@gmail.com
	auth_password=clavefacil#xxx

###### #B

Configurar en wampserver abrir ´Wampserver > PHP > php.ini´
buscar: sendmail_path  

	sendmail_path = C:\wamp\sendmail\sendmail.exe -t

También cambiar la configuracion del SMTP, cambiar el puerto

		[mail function]
	smtp_port = 465

