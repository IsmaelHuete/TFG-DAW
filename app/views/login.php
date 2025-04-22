<!DOCTYpE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/comun.css">
    <link rel="stylesheet" href="css/registro.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

</head>

<body>
    <main>
        <div class="registro">
            <img src="/img/image-brand.png">
            <h2>Inicia sesion</h2>
            <form>
                <input type="text" placeholder="Nombre completo" required>
                <input type="password" placeholder="Contraseña" required>
                <button type="submit">Iniciar sesion</button>
            </form>
            <span> <a href="#">¿Olvidaste tu contraseña?</a></span>
            <p>No tienes una cuenta?</p>
            <a href="register">Registrate</a>
        </div>
    </main>
    <div class="difuminado"></div>
    <?php 
        include ("layouts/footer2.php");
    ?> 
    <script src="js/header.js"></script>
    <script src="js/home.js"></script>
    
</body>
</html>