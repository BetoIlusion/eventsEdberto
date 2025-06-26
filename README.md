
NOMBRE: Edberto Ybanera Herrera

Iniciacion:
composer install

php artisan migrate

php artisan serve --host=[ipconfig]


ENFOQUE:
 Está pensado para que cualquier centro de convenciones pueda insertar 
 un evento a su criterio(sala seleccionada, o sala "automatica"), porque
 puede que una sala sea mas grande que otra y sabrá cual sala insertar, puede que lo identifique
 por el nombre de la sala, que solo la empresa sabe esa situacion, tambien
 consideré la fecha por que los eventos en su funcionamento son futuras,
 por lo tanto se ingresa fecha presente y futura.
 los eventos inician en crear salas de manera directa donde el 
 nombre se define por su id por creacion: 'room 1', 'room 2',..., 'room 3'. 
 luego los eventos se crean de 2 enfoques: insertando todos los atributos 
 (nombre,sala,horario) y otra de insercion pero sin ingresar la sala 
 donde el sistema automaticamente seleccione una sala sin solapamiento caso
contrario otra sala.

DISEÑO:
    Usé la arquitectura MVC, por conocimiento y práctica diaria,también
    por Laravel que se adapta mas a esta arquitectura donde es más facil
    mantener la logica de negocio separado, y se adapte a cualqueir cambio.
    No realice ningun Patron de diseño porque no vi necesario, ya que
     esto se implementa cuando el proyecto se llega a volver insostenible o volverse un proyecto grande
    con varias funcionalidades. por ejemplo si existiera varios tipos de eventos
    y cada evento al crear sea estrictemente filtrado lo que se generará sea
     "eventos recurrentes" un formato para crear registro otro de 
     "eventos para fiestas" también con su propio formato y asi respectivamente.

CODIFICACION:
    - Cumplo un formato de validacion de datos ingresados, para saber que datos
    son los que ingresan antes que se envien a la logica de negocio
    - Mantengo la funcion de lo que es un controlador que se encarga de 
    validar y enviar los datos al modelo para procesarlo ahi, si el caso es
    una llamada de datos sencilla no realizo tal actividad por el hecho de
    ser necesario por no ser elavarado o importante
    - Informo situaciones distintas de datos que el usuario ingrese para
    adaptarse a cualquier situacion que pueda ocurrir y recibir X mensaje


PRUEBAS UNITARIAS
: Todas las pruebas directas en el link de postman
https://bold-astronaut-377045.postman.co/workspace/My-Workspace~51d7d2ac-4eec-40c4-8cda-3894be8b7455/collection/30713590-2cc45b83-4172-44d4-82e1-fad42fbf54fe?action=share&creator=30713590&active-environment=30713590-a9ba6ef3-442c-49f1-956b-daa2b423a3f7

+ registra eventos conciderando unicidad del nombre
+ rechaza eventos por solapamiento en misma sala
+ permite eventos en misma hora pero distintas salas
+ cancela evento correctamente
+ consulta eventos activos en rango de horario y fecha seleccionada(criterio
    planteado en el enfoque)
+ muestra reporte de ocupacion porcentual, tiempo; respecto a la fecha establecida

LAS INSTRUCCIONES ESTAN EN EL POSTMAN PERO AQUI TAMBIEN LOS VOY A ESCRIBIR:
solo necesitas configurar sus credenciales de su base de datos en el .env : localhost/api
INICIAR APIs:
Room
+ crea sala : solo ejecutar SEND
+ all salas : solo ejecutar SEND
+ cambiar nombre : tiene su formato en el Body y ejecutar SEND

Events
+ crear evento con sala : tiene su formato en el Body y ejecutar SEND
+ crear evento sin sala : tiene su formato en el Body y ejecutar SEND
+ eventos activos : tiene su formato en el Body y ejecutar SEND
+ cancelar evento : : tiene su formato en el Body y ejecutar SEND
+ reporteOcupacionPorcentual : tiene su formato en el Body y ejecutar SEND


CASOS LIMITES
+ registra eventos de tiempo de diferencia de 1 min(ej: 09:00 - 09:01)
+ Evento con hora_inicial > hora_final (rechazado)
+ cancela evento correctamente, si se cancela un evento, 
    ya no se puede volver a cancelar, por lo tanto se toma que no existe
    tal evento y se podrá crear otro evento que puede que tenga el mismo nombre
    y crearse es evento

FORMATO DE ENTRADA Y SALIDA
: simple API 
    Uso este formato porque es mas practico para realizar pruebas de backend(pruebas en postman), 
    necesario para separacion backend-frontend si la idea de laravel usarlo solo para ese propósito,
    microservicios. Lo cual es el unico que tengo conocimiento necesario para realizar el proyecto es por API

