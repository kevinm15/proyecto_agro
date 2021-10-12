<?php
require_once('bdd.php');


$sql = "SELECT ci.idcita,CONCAT(hr.fecha,' ',h.nombre) as fechahora,CONCAT(p.nombres,' ',p.apellidos) as paciente,
ci.observaciones,ci.estado FROM cita as ci  INNER JOIN detalle_horario as dh
 on dh.iddetalle_horario=ci.iddetalle_horario INNER JOIN hora as h on h.idhora=dh.idhora
 INNER JOIN horario as hr on hr.idhorario=dh.idhorario INNER JOIN paciente as p on p.idpaciente=ci.idpaciente";

$req = $bdd->prepare($sql);
$req->execute();

$events = $req->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Inicio</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

	<!-- FullCalendar -->
	<link href='css/fullcalendar.css' rel='stylesheet' />


    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;

    }
	#calendar {
		max-width: 800px;
	}
	.col-centered{
		float: none;
		margin: 0 auto;
	}
    </style>



</head>

<body>

    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div id="calendar" class="col-centered">
                </div>
            </div>

        </div>
    </div>
    <!-- /.container -->

    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

	<!-- FullCalendar -->
	<script src='js/moment.min.js'></script>
	<script src='js/fullcalendar/fullcalendar.min.js'></script>
	<script src='js/fullcalendar/fullcalendar.js'></script>
	<script src='js/fullcalendar/locale/es.js'></script>

<link href='core/main.css' rel='stylesheet' />
  <link href='daygrid/main.css' rel='stylesheet' />
  <link href='timegrid/main.css' rel='stylesheet' />
  <link href='list/main.css' rel='stylesheet' />
	<script>

	$(document).ready(function() {

		var date = new Date();
       var yyyy = date.getFullYear().toString();
       var mm = (date.getMonth()+1).toString().length == 1 ? "0"+(date.getMonth()+1).toString() : (date.getMonth()+1).toString();
       var dd  = (date.getDate()).toString().length == 1 ? "0"+(date.getDate()).toString() : (date.getDate()).toString();

		$('#calendar').fullCalendar({
       plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
			header: {
				 language: 'es',
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay,listMonth',

			},
			defaultDate: yyyy+"-"+mm+"-"+dd,
			editable: false,
      buttonIcons: false,
      weekNumbers: true,
      navLinks: true,
			eventLimit: true, // allow "more" link when too many events
			selectable: false,
			selectHelper: false,
			events: [
			<?php foreach($events as $event):
			?>
				{
					title: '<?php echo $event['paciente']; ?>',
					start: '<?php echo $event['fechahora']; ?>',
				},
			<?php endforeach; ?>
			]
		});
	});

</script>

</body>

</html>
