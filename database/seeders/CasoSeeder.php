<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CasoSeeder extends Seeder
{
    public function run(): void
    {
        // Casos con fechas de 2026
        DB::table('casos')->insert([
            // ========= Cliente 1: María José Reyes (divorcio contencioso) =========
            [
                'caso_numero_expediente' => '0501-2026-00431',
                'cliente_id' => 1,
                'demandado_id' => 1,
                'tipo_tramite_id' => 2, // Divorcio contencioso
                'estado_id' => 6,        // Audiencia señalada
                'procurador_id' => 1,    // Iris Rodríguez
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-7',
                'caso_fecha_interpuesta' => '2026-01-15',
                'caso_relacion_hechos' => 'La señora María José Reyes Padilla contrae matrimonio civil con el señor Carlos Fernando Rivera Mendoza el 10 de marzo de 2018, procreando dos hijos: Sofía (6) y Mateo (4). Desde enero de 2025, el señor Rivera ha abandonado el hogar conyugal, incumpliendo con sus obligaciones de esposo y padre. La demandante solicita divorcio por causal de abandono voluntario del hogar, así como la custodia de los menores y pensión alimenticia.',
                'caso_observaciones_director' => 'Caso complejo con menores de por medio. Se recomienda llegar a un acuerdo conciliatorio.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-01-16',
                'caso_estado' => 'activo',
                'created_at' => '2026-01-15 10:00:00',
                'updated_at' => '2026-06-15 14:00:00',
            ],
            // ========= Cliente 2: Pedro Mejía (ejecución forzosa) =========
            [
                'caso_numero_expediente' => '0501-2026-00425',
                'cliente_id' => 2,
                'demandado_id' => 2,
                'tipo_tramite_id' => 6, // Solicitud de ejecución forzosa
                'estado_id' => 1,        // Entrevista
                'procurador_id' => 5,    // Ena Flores
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-3',
                'caso_fecha_interpuesta' => '2026-06-01',
                'caso_relacion_hechos' => 'Mediante sentencia firmemente ejecutoriada del Juzgado de Letras de la Sección Judicial de San Pedro Sula, se condenó al señor Carlos Fernando Rivera Mendoza al pago de CINCUENTA MIL LEMPIRAS (L. 50,000.00) en concepto de daños y perjuicios. A la fecha, el demandado no ha cumplido voluntariamente con la sentencia. Se solicita mandamiento de ejecución forzosa y embargo de bienes.',
                'caso_observaciones_director' => 'Ejecución de sentencia clara. El título ejecutivo es válido.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-06-01',
                'caso_estado' => 'activo',
                'created_at' => '2026-06-01 09:30:00',
                'updated_at' => '2026-06-03 11:00:00',
            ],
            // ========= Cliente 3: Ana Cecilia García (alimentos) =========
            [
                'caso_numero_expediente' => '0501-2026-00429',
                'cliente_id' => 3,
                'demandado_id' => 3,
                'tipo_tramite_id' => 3, // Demanda de alimentos
                'estado_id' => 4,        // Presentado al juzgado
                'procurador_id' => 2,    // Franklyn Salgado
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-8',
                'caso_fecha_interpuesta' => '2026-05-20',
                'caso_relacion_hechos' => 'La señora Ana Cecilia García Hernández tiene tres hijos menores de edad procreados con el señor Diego Alejandro Mendoza García: Carlos (10), Lucía (7) y Diego (5). El señor Mendoza se ha negado rotundamente a proporcionar alimentos para sus hijos desde septiembre de 2025, a pesar de tener capacidad económica como propietario del Taller El Chele. La demandante solicita una pensión alimenticia proporcional a los ingresos del demandado.',
                'caso_observaciones_director' => 'Caso urgente por tratarse de menores. Priorizar.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-05-21',
                'caso_estado' => 'activo',
                'created_at' => '2026-05-20 08:00:00',
                'updated_at' => '2026-06-12 10:30:00',
            ],
            // ========= Cliente 4: Bernarda Paz (revisión alimentos) =========
            [
                'caso_numero_expediente' => '0501-2026-00428',
                'cliente_id' => 4,
                'demandado_id' => 4,
                'tipo_tramite_id' => 4, // Revisión de demanda de alimentos
                'estado_id' => 6,        // Audiencia señalada
                'procurador_id' => 1,    // Iris Rodríguez
                'caso_parte_representada' => 'Demandada',
                'caso_juzgado' => 'J-3',
                'caso_fecha_interpuesta' => '2026-03-10',
                'caso_relacion_hechos' => 'Mediante sentencia del Juzgado de Familia, se fijó pensión alimenticia de CINCO MIL LEMPIRAS (L. 5,000.00) mensuales a favor de los hijos menores Andrés y Pamela Paz Mejía. El demandante, señor Manuel de Jesús Paz Mejía, solicita reducción de la pensión alegando disminución de ingresos. La señora Bernarda Aracely Paz Guzmán, representada por este consultorio, se opone a la reducción por cuanto los gastos de los menores han aumentado.',
                'caso_observaciones_director' => 'Defender el interés de los menores. Los gastos han subido considerablemente.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-03-10',
                'caso_estado' => 'activo',
                'created_at' => '2026-03-10 11:00:00',
                'updated_at' => '2026-06-10 15:00:00',
            ],
            // ========= Cliente 5: Luis Mendoza (reconocimiento paternidad) =========
            [
                'caso_numero_expediente' => '0501-2026-00427',
                'cliente_id' => 5,
                'demandado_id' => 5,
                'tipo_tramite_id' => 5, // Reconocimiento forzoso de paternidad
                'estado_id' => 3,        // Poder conferido
                'procurador_id' => 3,    // Indira Galindo
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-7',
                'caso_fecha_interpuesta' => '2026-04-05',
                'caso_relacion_hechos' => 'El señor Luis Enrique Mendoza Rivera sostuvo una relación sentimental con la señora Laura Beatriz Pérez Martínez, de la cual nació su hijo común. El demandante ha reconocido voluntariamente al menor y desea que se establezca legalmente la filiación paterna mediante la prueba de ADN correspondiente, así como que se fije un régimen de visitas y se garantice el derecho del menor a llevar su apellido paterno.',
                'caso_observaciones_director' => 'Voluntad expresa del padre de reconocer. Caso de mutuo acuerdo.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-04-06',
                'caso_estado' => 'activo',
                'created_at' => '2026-04-05 14:00:00',
                'updated_at' => '2026-06-08 09:00:00',
            ],
            // ========= Cliente 6: Karla Sandoval (divorcio contencioso) =========
            [
                'caso_numero_expediente' => '0501-2026-00433',
                'cliente_id' => 6,
                'demandado_id' => 7,
                'tipo_tramite_id' => 2, // Divorcio contencioso
                'estado_id' => 5,        // Admitido por el juzgado
                'procurador_id' => 4,    // Carlos Brizuela
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-5',
                'caso_fecha_interpuesta' => '2026-04-20',
                'caso_relacion_hechos' => 'La señora Karla Patricia Sandoval Torres contrajo matrimonio con el señor José Armando Rivas Castro el 5 de diciembre de 2019, procreando un hijo, Jorge (3 años). La demandante alega incompatibilidad de caracteres graves e incumplimiento de deberes matrimoniales por parte del demandado. Solicita divorcio, custodia del menor y pensión alimenticia.',
                'caso_observaciones_director' => 'Separación de hecho desde hace 10 meses. Hay voluntad de acuerdo.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-04-21',
                'caso_estado' => 'activo',
                'created_at' => '2026-04-20 10:00:00',
                'updated_at' => '2026-06-14 16:00:00',
            ],
            // ========= Cliente 7: Roberto Murillo (disolución mutuo acuerdo) =========
            [
                'caso_numero_expediente' => '0501-2026-00430',
                'cliente_id' => 7,
                'demandado_id' => null, // No aplica, es mutuo acuerdo... usaremos demandado_id=5 que es la esposa
                'tipo_tramite_id' => 1, // Disolución por mutuo acuerdo
                'estado_id' => 5,        // Admitido por el juzgado
                'procurador_id' => 4,    // Carlos Brizuela
                'caso_parte_representada' => 'Ambas partes',
                'caso_juzgado' => 'J-3',
                'caso_fecha_interpuesta' => '2026-05-02',
                'caso_relacion_hechos' => 'Los señores Roberto Carlos Murillo Díaz y Laura Beatriz Pérez Martínez, ambos mayores de edad y en pleno ejercicio de sus derechos civiles, solicitan al juzgado la disolución del vínculo matrimonial por mutuo consentimiento. Han llegado a un acuerdo integral sobre división de bienes (casa en Col. San Carlos y un vehículo) y custodia compartida de sus dos hijos menores. Presentan el convenio regulador firmado por ambas partes.',
                'caso_observaciones_director' => 'Acuerdo integral completo. Procedimiento expedito.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-05-02',
                'caso_estado' => 'activo',
                'created_at' => '2026-05-02 09:00:00',
                'updated_at' => '2026-06-14 12:00:00',
            ],
            // ========= Cliente 8: Diana Ortiz (alimentos - caso especial) =========
            [
                'caso_numero_expediente' => '0501-2026-00434',
                'cliente_id' => 8,
                'demandado_id' => null,
                'tipo_tramite_id' => 3, // Demanda de alimentos
                'estado_id' => 11,       // Atrasado
                'procurador_id' => 3,    // Indira Galindo
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-8',
                'caso_fecha_interpuesta' => '2026-02-15',
                'caso_relacion_hechos' => 'La señora Diana Patricia Ortiz Medina solicita pensión alimenticia para su hija menor de edad, fruto de una relación con el señor Ricardo Antonio Toro Quintanilla. El demandado ha evadido reiteradamente la notificación judicial y no se ha presentado a las audiencias señaladas.',
                'caso_observaciones_director' => 'El demandado no ha sido localizado. Se podría solicitar edictos.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-02-16',
                'caso_estado' => 'activo',
                'created_at' => '2026-02-15 08:30:00',
                'updated_at' => '2026-06-01 10:00:00',
            ],
            // ========= Cliente 9: Jorge Rivera (alimentos) =========
            [
                'caso_numero_expediente' => '0501-2026-00435',
                'cliente_id' => 9,
                'demandado_id' => 6,
                'tipo_tramite_id' => 3, // Demanda de alimentos
                'estado_id' => 2,        // Admitido
                'procurador_id' => 2,    // Franklyn Salgado
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-4',
                'caso_fecha_interpuesta' => '2026-06-10',
                'caso_relacion_hechos' => 'El señor Jorge Luis Rivera Murillo solicita revisión de la pensión alimenticia que actualmente paga a favor de sus hijos menores, por cuanto ha sufrido una reducción significativa en sus ingresos debido al cierre de su negocio. Actualmente paga L. 8,000.00 mensuales y solicita reducción a L. 4,000.00.',
                'caso_observaciones_director' => 'Evaluar capacidad económica real del demandante.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-06-10',
                'caso_estado' => 'activo',
                'created_at' => '2026-06-10 11:00:00',
                'updated_at' => '2026-06-10 11:00:00',
            ],
            // ========= Cliente 10: Sandra Cruz (alimentos) =========
            [
                'caso_numero_expediente' => '0501-2026-00436',
                'cliente_id' => 10,
                'demandado_id' => 3,
                'tipo_tramite_id' => 3, // Demanda de alimentos
                'estado_id' => 1,        // Entrevista
                'procurador_id' => 5,    // Ena Flores
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-8',
                'caso_fecha_interpuesta' => '2026-06-20',
                'caso_relacion_hechos' => 'La señora Sandra Elizabeth Cruz Flores, mujer de escasos recursos económicos, solicita pensión alimenticia para sus dos hijos menores, Luis (7) y Kevin (4), procreados con el señor Diego Alejandro Mendoza García. La demandante no cuenta con un trabajo fijo y depende de la ayuda familiar.',
                'caso_observaciones_director' => 'Apadrinar caso por situación de vulnerabilidad.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-06-20',
                'caso_estado' => 'activo',
                'created_at' => '2026-06-20 09:00:00',
                'updated_at' => '2026-06-20 09:00:00',
            ],
            // ========= Cliente 11: Héctor Vargas (divorcio contencioso - cerrado) =========
            [
                'caso_numero_expediente' => '0501-2026-00432',
                'cliente_id' => 11,
                'demandado_id' => 6,
                'tipo_tramite_id' => 2, // Divorcio contencioso
                'estado_id' => 8,        // Cerrado
                'procurador_id' => 1,    // Iris Rodríguez
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-6',
                'caso_fecha_interpuesta' => '2025-11-03',
                'caso_relacion_hechos' => 'El señor Héctor Daniel Vargas Pinto solicitó divorcio contencioso por causal de separación de hecho prolongada. El proceso culminó con sentencia favorable el 20 de mayo de 2026, disolviendo el vínculo matrimonial y estableciendo un régimen de visitas para la hija menor, Camila.',
                'caso_observaciones_director' => 'Caso cerrado exitosamente. Sentencia firme.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2025-11-04',
                'caso_estado' => 'cerrado',
                'created_at' => '2025-11-03 10:00:00',
                'updated_at' => '2026-05-20 16:00:00',
            ],
            // ========= Cliente 12: Dilcia Quintanilla (disolución mutuo acuerdo) =========
            [
                'caso_numero_expediente' => '0501-2026-00437',
                'cliente_id' => 12,
                'demandado_id' => 8,
                'tipo_tramite_id' => 1, // Disolución por mutuo acuerdo
                'estado_id' => 7,        // En sentencia
                'procurador_id' => 1,    // Iris Rodríguez
                'caso_parte_representada' => 'Ambas partes',
                'caso_juzgado' => 'J-3',
                'caso_fecha_interpuesta' => '2026-04-01',
                'caso_relacion_hechos' => 'Los señores Dilcia Nohemí Quintanilla Paz y Ricardo Antonio Toro Quintanilla solicitan conjuntamente la disolución del matrimonio civil celebrado el 15 de agosto de 2015. Han llegado a un acuerdo sobre: custodia compartida de los hijos Fernando (8) y Gabriela (6), pensión alimenticia de L. 6,000.00 mensuales, y división de bienes (casa en Villa Olímpica y un vehículo).',
                'caso_observaciones_director' => 'Acuerdo integral. Procedimiento expedito.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-04-01',
                'caso_estado' => 'activo',
                'created_at' => '2026-04-01 13:00:00',
                'updated_at' => '2026-06-18 11:00:00',
            ],
            // ========= Caso con demandado_id = null, probando nulidad =========
            [
                'caso_numero_expediente' => '0501-2026-00438',
                'cliente_id' => 1,
                'demandado_id' => null,
                'tipo_tramite_id' => 2, // Divorcio contencioso
                'estado_id' => 1,        // Entrevista
                'procurador_id' => 4,    // Carlos Brizuela
                'caso_parte_representada' => 'Demandante',
                'caso_juzgado' => 'J-7',
                'caso_fecha_interpuesta' => '2026-06-24',
                'caso_relacion_hechos' => 'Segunda demanda de divorcio presentada por la señora Reyes Padilla contra el señor Rivera Mendoza, añadiendo nuevas causales.',
                'caso_observaciones_director' => 'Evaluar si hay ne bis in ídem con el caso 00431.',
                'caso_admisible' => true,
                'caso_fecha_asignacion' => '2026-06-24',
                'caso_estado' => 'activo',
                'created_at' => '2026-06-24 15:00:00',
                'updated_at' => '2026-06-24 15:00:00',
            ],
        ]);
    }
}
