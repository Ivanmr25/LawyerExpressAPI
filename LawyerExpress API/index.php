<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require "conexion/Conexion.php";

$app = AppFactory::create();
$app->setBasePath("/lawyerexpress");
$conn = Conexion::getPDO();
//$app->get('/', function (Request $request, Response $response, $args) {
//    $response->getBody()->write("Hello world!");
//    return $response;
//});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('
    <h1 style=" align-items: center; background-color: #0B1B35; color: #C0C0FF; text-align:center;">
    <img src="https://i.pinimg.com/736x/3b/92/1c/3b921c51dc99d9fb2be192af3ec14f72.jpg" alt="Logo de LawyerExpress" width="50px" style="margin-right: 10px;">
    Welcome to Api LawyerExpress with Slim - Iván Mulero
  </h1>
    <table border="1" style="width: 40%;margin: 0 auto; background-color: #C0C0FF; color:#0B1B35;">
    <tr style="background-color: #0B1B35; color: #C0C0FF;"><th>Method</th><th>Url</th><th>Description</th></tr>
    <tr><td>get </td><td>/abogados</td><td>Lista de abogados</td></tr>
   <tr><td>get </td><td>/partidos</td><td>Lista de partidos</td></tr>
    <tr><td>get </td><td>/amigos/{numero_colegiado}</td><td>Lista de amigos</td></tr>
    <tr><td>get </td><td>/abogadouser/{numero_colegiado}</td><td>Objeto usuario si existe idusuario</td></tr>
    <tr><td>get </td><td>/telefono/{numero_colegiado}</td><td>Numero de Telefono de un abogado</td></tr>
    <tr><td>post</td><td>/abogadouser/</td><td>Add new abogado. <= Abogado</td></tr>
    <tr><td>post</td><td>/amigo/{numero}</td><td>Add new amigo. <= abogado</td></tr>
    <tr><td>post</td><td>/telefono/{numero_colegiado}/</td><td>Add new telefono al abogado. <= Telefono</td></tr>
    <tr><td>post</td><td>/ubicacion/{numero_colegiado}/</td><td>Update ubicacion from abogado. <= Abogado</td></tr>
    </table>
    ');
    return $response;
});

// Get bares
$app->get('/abogados', function ($request, $response, $args) use ($conn){
    $ordenSql = "select * from abogado order by nombre";
    $statement = $conn->prepare($ordenSql);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    $salidajson=json_encode(["abogados"=>$salida],JSON_UNESCAPED_UNICODE);
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
});

$app->get('/partidos', function ($request, $response, $args) use ($conn){
    $ordenSql = "select * from partidojudicial order by nombre";
    $statement = $conn->prepare($ordenSql);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    $salidajson=json_encode(["partidos"=>$salida],JSON_UNESCAPED_UNICODE);
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
});


$app->get('/amigos/{numero_colegiado}', function ($request, $response, $args) use ($conn) {
    $numero_colegiado = $args['numero_colegiado'];
    $ordenSql = "SELECT a.numero_colegiado, a.nombre, a.partidojudicial_id, a.latitud, a.longitud, a.pass 
                 FROM abogado a 
                 INNER JOIN amigo am ON a.numero_colegiado = am.amigo_Id 
                 WHERE am.numero_colegiado = :numero_colegiado";
    $statement = $conn->prepare($ordenSql);
    $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_STR);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    $salidajson = json_encode(["amigos" => $salida], JSON_UNESCAPED_UNICODE);
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
});



$app->get('/abogadouser/{numero_colegiado}', function (Request $request, Response $response, $args) use ($conn) {
    $idusuario = $args['numero_colegiado'];
    $pass = $request->getQueryParams()['pass'] ?? null;

    if (!isset($pass)) {
        $body = $request->getBody()->getContents();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $pass = $jsonobj->pass;
        }
    }

    $ordenSql = "SELECT * FROM abogado WHERE numero_colegiado = :numero_colegiado AND pass = :pass";
    $statement = $conn->prepare($ordenSql);
    $statement->bindParam(':numero_colegiado', $idusuario, PDO::PARAM_STR);
    $statement->bindParam(':pass', $pass, PDO::PARAM_STR);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;

    if ($salida != null) {
        $salidajson = json_encode(["abogado" => $salida[0]], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    } else {
        $salidajson = json_encode(["abogado" => null], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }
});


$app->get('/telefono/{numero_colegiado}', function ($request, $response, $args) use ($conn) {
    $idusuario = $args['numero_colegiado'];
    $ordenSql = "select * from telefono where numero_colegiado=:numero_colegiado";
    $statement = $conn->prepare($ordenSql);
    $statement->bindParam(':numero_colegiado', $idusuario, PDO::PARAM_STR);
    $statement->execute();
    $salida = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement = null;
    if ($salida != null) {
        $salidajson=json_encode(["telefono" => $salida[0]], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
    } else {
        $salidajson=json_encode(["telefono" => null], JSON_UNESCAPED_UNICODE);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
    }
});


$app->post('/abogadouser', function ($request, $response, $args) use ($conn) {

    $numero_colegiado = $request->getParsedBody()['numero_colegiado'] ?? null;
    $nombre = $request->getParsedBody()['nombre'] ?? null;
    $partidojudicial_id = $request->getParsedBody()['partidojudicial_id'] ?? null;
    $latitud = $request->getParsedBody()['latitud'] ?? null;
    $longitud = $request->getParsedBody()['longitud'] ?? null;
    $pass = $request->getParsedBody()['pass'] ?? null;

    // Si no vienen sueltas, lo mismo vienen dentro del body en formato JSON
    if (!isset($numero_colegiado) || !isset($nombre) || !isset($partidojudicial_id) || !isset($latitud) || !isset($longitud) || !isset($pass)) {
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $numero_colegiado = $jsonobj->{'numero_colegiado'};
            $nombre = $jsonobj->{'nombre'};
            $partidojudicial_id = $jsonobj->{'partidojudicial_id'};
            $latitud = $jsonobj->{'latitud'};
            $longitud = $jsonobj->{'longitud'};
            $pass = $jsonobj->{'pass'};
        }
    }
   

    try {
        if (!isset($nombre) || !isset($partidojudicial_id) || !isset($latitud) || !isset($longitud) || !isset($pass)) {
            $salidajson=json_encode(["msg"=>"No Data..."], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($salidajson);
            return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
        } else {
            $ordenSql = "INSERT INTO abogado(numero_colegiado, nombre, partidojudicial_id, latitud, longitud, pass) values(:numero_colegiado, :nombre, :partidojudicial_id, :latitud, :longitud, :pass)";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);
            $statement->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $statement->bindParam(':partidojudicial_id', $partidojudicial_id, PDO::PARAM_INT);
            $statement->bindParam(':latitud', $latitud, PDO::PARAM_STR);
            $statement->bindParam(':longitud', $longitud, PDO::PARAM_STR);
            $statement->bindParam(':pass', $pass, PDO::PARAM_STR);
            $conn->beginTransaction();
            $statement->execute();
            $idlast = $conn->lastInsertId();
            $conn->commit();
            $colegiado = ["numero_colegiado"=>$numero_colegiado, "nombre"=>$nombre, "partidojudicial_id"=>$partidojudicial_id, "latitud"=>$latitud, "longitud"=>$longitud, "pass"=>$pass];
        }
    } catch (PDOException $e) {
        $salidajson=json_encode([$e]);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    $salidajson=json_encode(["abogado"=>$colegiado]);
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json');
    
});


$app->post('/amigo/{numero}', function ($request, $response, $args) use ($conn) {
    try {
        $numero = $args['numero'];
        $numero_colegiado = $request->getParsedBody()['numero_colegiado'] ?? null;

        // Si no vienen sueltas, lo mismo vienen dentro del body en formato JSON
        if (!isset($numero) || !isset($numero_colegiado)) {
            $body = $request->getBody();
            $jsonobj = json_decode($body);
            if ($jsonobj != null) {
                $numero_colegiado = $jsonobj->{'numero_colegiado'};
            }
        }

        if (!isset($numero) || !isset($numero_colegiado)) {
            $salidajson = json_encode(["msg" => "No Data..."], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($salidajson);
            return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
        } else {
            // Verificar la existencia del número en la tabla telefono
            $numeroExistsSql = "SELECT COUNT(*) as count FROM telefono WHERE numero = :numero";
            $numeroExistsStatement = $conn->prepare($numeroExistsSql);
            $numeroExistsStatement->bindParam(':numero', $numero, PDO::PARAM_INT);
            $numeroExistsStatement->execute();
            $numeroExists = $numeroExistsStatement->fetch(PDO::FETCH_ASSOC);

            if ($numeroExists['count'] == 0) {
                $salidajson = json_encode(["msg" => "El número no existe"], JSON_UNESCAPED_UNICODE);
                $response->getBody()->write($salidajson);
                return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
            }

            $ordenSql = "INSERT INTO amigo (amigo_Id, numero_colegiado) 
            VALUES (
              (SELECT numero_colegiado FROM telefono WHERE numero = :numero),
              :numero_colegiado
            )";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':numero', $numero, PDO::PARAM_INT);
            $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);

            $conn->beginTransaction();
            $statement->execute();
            $conn->commit();

            // Obtén el objeto amigo insertado
            $ordenSqlAmigo = "SELECT amigo_Id, numero_colegiado
                              FROM amigo
                              WHERE numero_colegiado = :numero_colegiado";
            $statementAmigo = $conn->prepare($ordenSqlAmigo);
            $statementAmigo->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);
            $statementAmigo->execute();
            $amigo = $statementAmigo->fetch(PDO::FETCH_ASSOC);

            if ($amigo !== false) {
                $salidajson = json_encode(["amigo" => $amigo]);
                $response->getBody()->write($salidajson);
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $salidajson = json_encode(["msg" => "Error al obtener el amigo insertado"], JSON_UNESCAPED_UNICODE);
                $response->getBody()->write($salidajson);
                return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
            }
        }
    } catch (PDOException $e) {
        $salidajson = json_encode(["msg" => "$e"]);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json');
    }

    $salidajson = json_encode((["amigo" => $colegiado]));
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json');
});



$app->post('/telefono', function ($request, $response, $args) use ($conn) {

    $numero_colegiado = $request->getParsedBody()['numero_colegiado'] ?? null;
    $numero = $request->getParsedBody()['numero'] ?? null;
   

    // Si no vienen sueltas, lo mismo vienen dentro del body en formato JSON
    if (!isset($numero) || !isset($numero_colegiado) ) {
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $numero_colegiado = $jsonobj->{'numero_colegiado'};
            $numero = $jsonobj->{'numero'};
           
        }
    }

    try {
        if (!isset($numero) || !isset($numero_colegiado)) {
            $salidajson=json_encode(["msg"=>"No Data..."], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($salidajson);
            return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
        } else {
            $ordenSql = "INSERT INTO Telefono(numero,numero_colegiado) values(:numero, :numero_colegiado)";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':numero', $numero, PDO::PARAM_INT);
            $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);
          
        
            $conn->beginTransaction();
            $statement->execute();
            $idlast = $conn->lastInsertId();
            $conn->commit();
            $colegiado = ["numero"=>$numero,"numero_colegiado"=>$numero_colegiado];
        }
    } catch (PDOException $e) {
        $salidajson=json_encode(["msg"=>"$e"]);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    $salidajson=json_encode((["telefono"=>$colegiado]));
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json');
    
});






$app->post('/telefono/{numero_colegiado}', function ($request, $response, $args) use ($conn) {

    $numero_colegiado = $args['numero_colegiado'];
    $numero = $request->getParsedBody()['numero'] ?? null;
   

    // Si no vienen sueltas, lo mismo vienen dentro del body en formato JSON
    if (!isset($numero) ) {
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $numero = $jsonobj->{'numero'};
           
        }
    }

    try {
        if (!isset($numero)) {
            $salidajson=json_encode(["msg"=>"No Data..."], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($salidajson);
            return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
        } else {
            $ordenSql = "INSERT INTO Telefono(numero,numero_colegiado) values(:numero, :numero_colegiado)";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':numero', $numero, PDO::PARAM_INT);
            $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);
          
        
            $conn->beginTransaction();
            $statement->execute();
            $idlast = $conn->lastInsertId();
            $conn->commit();
            $colegiado = ["numero"=>$numero,"numero_colegiado"=>$numero_colegiado];
        }
    } catch (PDOException $e) {
        $salidajson=json_encode(["msg"=>"Violada Primary key..."]);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    $salidajson=json_encode((["telefono"=>$colegiado]));
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json');
    
});

$app->post('/ubicacion/{numero_colegiado}', function ($request, $response, $args) use ($conn) {
    $numero_colegiado = $args['numero_colegiado'];
    $nueva_latitud = $request->getParsedBody()['latitud'] ?? null;
    $nueva_longitud = $request->getParsedBody()['longitud'] ?? null;

    if (!isset($nueva_latitud) ||!isset($nueva_longitud) ) {
        $body = $request->getBody();
        $jsonobj = json_decode($body);
        if ($jsonobj != null) {
            $nueva_latitud= $jsonobj->{'latitud'};
            $nueva_longitud= $jsonobj->{'longitud'};
           
        }
    }

    try {
        if (!isset($nueva_latitud) || !isset($nueva_longitud)) {
            $salidajson = json_encode(["msg" => "No Data..."], JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($salidajson);
            return $response->withHeader('Content-Type', 'application/json; charset=UTF8');
        } else {
            $ordenSql = "UPDATE abogado SET latitud = :latitud, longitud = :longitud WHERE numero_colegiado = :numero_colegiado";
            $statement = $conn->prepare($ordenSql);
            $statement->bindParam(':latitud', $nueva_latitud, PDO::PARAM_STR);
            $statement->bindParam(':longitud', $nueva_longitud, PDO::PARAM_STR);
            $statement->bindParam(':numero_colegiado', $numero_colegiado, PDO::PARAM_INT);

            $conn->beginTransaction();
            $statement->execute();
            $conn->commit();

            $colegiado = [ "latitud" => $nueva_latitud, "longitud" => $nueva_longitud];
        }
    } catch (PDOException $e) {
        $salidajson = json_encode(["msg" => "Error updating position..."]);
        $response->getBody()->write($salidajson);
        return $response->withHeader('Content-Type', 'application/json');
    }

    $salidajson = json_encode(["Abogado" => $colegiado]);
    $response->getBody()->write($salidajson);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
?>