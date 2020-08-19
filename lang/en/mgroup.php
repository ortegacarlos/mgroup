<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_mgroup
 * @category    string
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Formación de Grupos colaborativos';
$string['modulename'] = 'Formación de Grupos Colaborativos';
$string['modulename_help'] = 'El módulo "Formación de Grupos Colaborativos" permite formar grupos para escenarios de trabajo colaborativo; formación basada en características de los participantes, como por ejemplo, rasgos de la personalidad.';
$string['modulenameplural'] = 'Formación de Grupos Colaborativos';

$string['mgroupname'] = 'Nombre del grupo';
$string['mgroupname_help'] = 'Digite el nombre identificador del nuevo grupo de trabajo colaborativo a crear.';

$string['nonewmodules'] = 'No hay Grupos de trabajo colaborativo';

// Add string by form
$string['groupsize'] = 'Tamaño del grupo';
$string['groupsize_help'] = 'Digite un valor entero correspondiente a la cantidad de individuos en cada grupo. El valor por defecto es de 3 individuos.';

$string['numberofcharacteristics'] = 'Cantidad de características';
$string['numberofcharacteristics_help'] = 'Digite un valor entero correspondiente a la cantidad de características de cada individuo necesarias para la formación de los grupos. El valor por defecto es de 5 características.';

$string['datasource'] = 'Seleccione una opción';
$string['datasource_help'] = 'Seleccione una opción si desea la formación de los grupos con datos de una instancia del módulo "Resultados BFI", de lo contrario seleccione la opción "Subir archivo" para utilizar sus valores y formar los grupos.';

$string['mbfi'] = 'Resultados previos';
$string['uploadfile'] = 'Subir archivo';

$string['mbfiar'] = 'Seleccione una instancia';
$string['mbfiar_help'] = 'Escoja una instancia para utilizar sus datos y formar los grupos.';

$string['populationsize'] = 'Tamaño de la población';
$string['populationsize_help'] = 'Digite un valor entero correspondiente al tamaño de la población a considerar. El valor por defecto es de 50 individuos.';

$string['selectionoperator'] = 'Operador de selección';
$string['selectionoperator_help'] = 'Digite un valor entero correspondiente al porcentaje a considerar para el operador de selección. El valor por defecto es de 40.';

$string['mutationoperator'] = 'Operador de mutación';
$string['mutationoperator_help'] = 'Digite un valor real correspondiente a la probabilidad a considerar para el operador de mutación. El valor por defecto es de 0.2.';

$string['numberofgenerations'] = 'Cantidad de generaciones';
$string['numberofgenerations_help'] = 'Digite un valor entero correspondiente a la cantidad máxima de generaciones para el ciclo de evolución. El valor por defecto es de 150 generaciones.';

//$string['groupingmbfi'] = 'Fuente de datos';
//$string['groupingmbfi_help'] = 'Seleccione una opción si desea formar los grupos con los resultados obtenidos del módulo "M-BFI", de lo contrario seleccione la opción "Subir archivo" para utilizar sus valores y formar los grupos.';

$string['userfile'] = 'Archivo de datos';
$string['userfile_help'] = 'Campo para cargar un archivo de texto plano, con valores separados por comas (.csv), que almacena los datos y cuantificación de las características de los individuos requeridos para la formación de grupos de trabajo colaborativo.';

$string['enrolled'] = 'Permitir estudiantes no matriculados en el curso';
$string['enrolled_help'] = 'Seleccione esta opción si desea permitir la formación de grupos con estudiantes no matriculados en su curso.';

$string['groupingparameters'] = 'Parámetros de agrupamiento';
$string['groupingsettings'] = 'Ajustes de agrupamiento';
$string['homogeneous'] = 'Homogéneo';
$string['heterogeneous'] = 'Heterogéneo';
$string['mixed'] = 'Mixto';

$string['groupingtypear'] = 'Tipo de agrupamiento';
$string['groupingtypear_help'] = 'Seleccione el tipo de agrupamiento que desea utilizar al momento de hacer la formación de grupos. El agrupamiento mixto toma un combinación de características homogéneas y heterogéneas al momento de hacer la formación de grupos.';

$string['grouphomocharacteristic'] = 'Homogéneo para...';
$string['grouphomocharacteristic_help'] = 'Seleccione las características que serán tomadas como homogéneas al momento de la formación de grupos.';

$string['grouphetecharacteristic'] = 'Heterogéneo para...';
$string['grouphetecharacteristic_help'] = 'Seleccione las características que serán tomadas como heterogéneas al momento de la formación de grupos.';

$string['group'] = 'Grupo';

// Add string admin settings
$string['defaultsettings'] = 'Configuración por defecto';
$string['javaserver'] = 'Dirección de la aplicación PHP/Java Bridge';
$string['javaserver_desc'] = 'La dirección por defecto accede a la aplicación que escucha en la interfaz local. Si la aplicación se encuentra ejecuntando fuera de un ambiente local, por favor ingrese la dirección correspondiente.';

// Add string errors
$string['err_groupsize'] = 'Debe suministrar un valor entero mayor a 0.';
$string['err_groupsizedb'] = 'El tamaño del grupo no corresponde con el guardado en la base de datos.';
$string['err_numberofcharacteristics'] = 'Debe suministrar un valor entero mayor a 0.';
$string['err_populationsize'] = 'Debe suministrar un valor entero mayor a 0.';
$string['err_selectionoperator'] = 'Debe suministrar un valor entero entre 1 y 100.';
$string['err_mutationoperator'] = 'Debe suministrar un valor entre 0 y 1.';
$string['err_numberofgenerations'] = 'Debe suministrar un valor entero mayor a 0.';
$string['err_savefile'] = 'Error al guardar el archivo o  no se cargó ningún archivo previamente.';
$string['err_createfile'] = 'Error al crear el archivo.';
$string['err_deletefile'] = 'Error al eliminar el archivo.';
$string['err_readfile'] = 'Error al leer el archivo.';
$string['err_checkfile'] = 'Revise las inconsistencias encontradas en el archivo.';
$string['err_checkparameters'] = 'Hay inconsistencias en la línea {$a->number}. El número de características ingresadas no coincide con el número de características guardadas en el archivo.';
$string['err_checkusers'] = 'Revise que los estudiantes se encuentran matriculados en el curso.';
$string['err_user'] = '{$a->name} no se encuentra matriculado en el curso.';

// Add string PDF file
$string['title_file'] = 'Grupos de Trabajo Colaborativo';
$string['teacher_course'] = 'Docente del curso: ';
$string['keywords_file'] = 'Moodle, Grupo de trabajo, Trabajo colaborativo';
$string['subject_file'] = 'M-GROUP: Complemento de Moodle para la Formación de Grupos Colaborativos';
$string['list_file'] = 'Listado de Grupos Colaborativos';
$string['general_information_file'] = 'Información General';
$string['date_file'] = 'Creado en: ';
$string['course_file'] = 'Curso: ';
$string['downloadpdf'] = 'Descargar PDF';