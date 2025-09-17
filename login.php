<?php
require_once 'conexion/conexion.php';
require_once 'Auth.php';

// Configuración inicial de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo limpiar sesión si no es una solicitud AJAX
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    session_unset();
    session_destroy();
    session_start();
}

$conn = conectate();
$auth = new Auth($conn);

$ano = date("Y");

// Manejar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    
    if ($auth->login($username, $password)) {
        echo json_encode(['success' => true, 'redirect' => 'index.php']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Usuario o contraseña incorrectos']);
    }
    exit();
}

// Manejar POST normal (por si acaso)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    
    if ($auth->login($username, $password)) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Ventas por Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Include Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Include jQuery (Toastr depends on it) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Include Toastr JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <style>
        body {
            background: linear-gradient(to right, #004080, #0080ff); /* Colores de Bahía Supermarket */
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            background: white;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #004080;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.4);
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            display: block;
            margin: 0 auto 15px;
            width: 120px;
            animation: spinShrink 2s ease-in-out;
        }

        @keyframes spinShrink {
            0% {
                transform: scale(1.5) rotate(0deg);
                opacity: 0;
            }
            100% {
                transform: scale(1) rotate(360deg);
                opacity: 1;
            }
        }

        .form-container {
            padding: 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.9);
            /* box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); */
        }
        .form-control {
            border: 2px solid #004080;
            background: #f0f8ff;
            color: #004080;
        }
        .form-control:focus {
            border-color: #0080ff;
            box-shadow: 0 0 5px rgba(0, 128, 255, 0.8);
        }
        .alert {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg p-4">
                	<br>
                    <img src="imagenes/logo_solo_amarillo.png" alt="Bahía Supermarket" class="logo">
                    <br>
                    <div class="title">Control de Ventas <br> Delivery</div>
                    
  

                    <div class="form-container">
                        <form id="loginForm" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Usuario</label>
                                <input type="text" class="form-control" id="username" name="username"  autofocus>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password" >
                            </div>
                            <div class="d-grid">
                                <button id="btn_aceptar" type="button" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Ingresar
                                </button>
                            </div>
                        </form>
                    </div>
                    
                  <!-- Alertas dinámicas -->
                    <div id="alertaCampos" class="alert alert-danger" role="alert">
                        ⚠️ Todos los campos son obligatorios.
                    </div>
                    <div id="alertaCredenciales" class="alert alert-warning" role="alert">
                        ❌ Usuario o contraseña incorrectos.
                    </div>                    
                    
                </div>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    // Manejar el evento de click
    $("#btn_aceptar").click(function(event) {
        event.preventDefault();
        let username = $("#username").val().trim();
        let password = $("#password").val().trim();
        
        if (username === "" || password === "") {
            toastr.warning('Todos los campos son obligatorios', 'Advertencia', {
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "2000",
                "positionClass": "toast-top-center"
            });
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "login.php",
            dataType: "json",
            data: {
                username: username,
                password: password
            },
            beforeSend: function() {
                // Mostrar carga (opcional)
                $("#btn_aceptar").prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Verificando...');
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    toastr.error(response.error, 'Error', {
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "2000",
                        "positionClass": "toast-top-center"
                    });
                }
            },
            error: function(xhr, status, error) {
                toastr.error('Error en la comunicación con el servidor', 'Error', {
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "2000",
                    "positionClass": "toast-top-center"
                });
            },
            complete: function() {
                $("#btn_aceptar").prop("disabled", false).html('<i class="fas fa-sign-in-alt"></i> Ingresar');
            }
        });
    });
    
    // Permitir login con Enter
    $("#username, #password").keypress(function(e) {
        if (e.which == 13) {
            $("#btn_aceptar").click();
        }
    });
});
</script>
</body>
</html>