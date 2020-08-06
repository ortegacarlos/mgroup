# Información General #

El módulo permite la formación de grupos con un enfoque colaborativo dentro
de un curso, con un tamaño de grupo y características de agrupamiento que el
docente del curso considere apropiadas.

Requiere como fuente de datos de un archivo de texto plano, con valores
separados por comas, donde se encuentra los datos de los individuos y la
cuantificación de sus características, o permite utilizar como fuente de datos
los valores obtenidos por una instancia del complemento **M-BFI**.

El módulo permite hacer la formación de los grupos con estudiantes que no se
encuentren matriculados en el curso sólo si la opción de permitir se encuentra
habilitada.

## Requisitos de Funcionamiento ##

Para instalar y poner en funcionamiento el complemento, se debe tener en cuenta
los siguientes requisitos:

* Un servidor de aplicaciones Java como Glassfish, JBoss, Tomcat, etc.
* Un servidor web que interprete scripts de PHP como Apache, IIS, etc.
* Para mayor información relacionada con los servidores de aplicaciones Java y
  Web soportados se puede dirigir a su [sitio](http://php-java-bridge.sourceforge.net/pjb/installation.php).

## Instalación del Adaptador PHP/JAVA Bridge ##

PHP/Java Bridge permite conectar un motor de script nativo como PHP con una
máquina virtual Java, permite una comunicación más rápida y más confiable que
la comunicación directa a través de la interfaz nativa de Java, y no se
requiere de componentes adicionales para invocar procedimientos de Java desde
PHP o procedimientos PHP desde Java.

Antes de hacer la instalación del adaptador se debe verificar los siguientes
requisitos:

* Se necesita de Java 1.4 o superior ejecutándose en cualquier sistema
  operativo o arquitectura, puesto que PHP/Java Bridge es una aplicación Java
  JEE pura.
* Se recomienda Apache Tomcat 7 o superior, o cualquier servidor JEE estándar o
  un motor de servlet para ejecutar código Java.

Una vez hecha a comprobación, se debe llevar a cabo la instalación del adaptador:

* Instalar la aplicación [JavaBridgeTemplate721.war](http://sourceforge.net/projects/php-java-bridge/files/Binary%20package/php-java-bridge_7.2.1/JavaBridgeTemplate721.war/download)
  en su servidor de aplicaciones Java.
* Modificar la línea `allow_url_include=On` del archivo de configuración
  **php.ini** de PHP.
* Se puede encontrar más información relacionada con la aplicación o con el
  proceso de instalación en su [sitio](http://php-java-bridge.sourceforge.net/).

## Instalación del módulo ##

* Verificar que la versión de Moodle sea igual o superior a la 3.0.
* Escoger una de las 3 formas de llevar a cabo la instalación del módulo:
  * Buscar e instalar directamente desde el directorio de [plugins](https://moodle.org/plugins/).
  * Instalar módulos externos desde la administración del sitio mediante un
    archivo ZIP.
  * Descomprimir el archivo y copiar la carpeta en el directorio `/mod` ubicado
    en el directorio raíz de la instalación.
* En **Nuevos ajustes** ingresar la dirección web que apunta al archivo
  `Java.inc` ubicado dentro del directorio raíz donde se ejecuta la
  aplicación Java Bridge.<br>
  La siguiente es la estructura de la dirección que se debe proporcionar
  `http://dominio-o-ip-del-servidor:puerto-de-escucha/nombre-aplicación-php-java-bridge/java/Java.inc`.

## Archivo de Datos ##
Los datos de los individuos a agrupar, incluyendo la cuantificación de sus
características, se leen desde un archivo de texto plano, con valores separados
por comas (.csv), con una estructura por registro similar a la que se muestra:

ID|NOMBRE|CORREO|C1|C2|C3|C4|C5|...|Cn
--|------|------|--|--|--|--|--|---|--
6|INDIVIDUO6|INDIVIDUO6@PRUEBA.COM|4.000|3.000|3.7778|3.125|4.400|...|n

El campo ID es un número identificador único de cada individuo que debe
corresponder con el nombre de usuario del individuo sólo si éste se encuentra
registrado en la plataforma, esto con el fin de comprobar que todos los
individuos se encuentran matriculados en el curso si la opción que permita la
formación de los grupos con estudiantes no matriculados no ha sido
activada. Cada uno de los valores de las características consideradas,
provienen de la aplicación de algún tipo de instrumento o test que permiten su
denominación y cuantificación, por ejemplo, el *"Big Five Inventory – BFI"*. 

## Funcionamiento ##
1. Crear una actividad de **Formación de Grupos Colaborativos**.
2. Diligenciar el formulario con los valores que considere adecuados.
3. Guardar y mostrar los resultados.

## License ##

Carlos Ortega <carlosortega@udenar.edu.co><br>
Oscar Revelo Sánchez <orevelo@udenar.edu.co><br>
Jesús Insuasti Portilla <insuasty@udenar.edu.co><br>
2019

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
