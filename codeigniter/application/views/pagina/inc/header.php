<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hemeroteca José Antonio Arze - UMSS</title>
    
    <!-- CSS Files -->
    <link href="<?= base_url('adminXeria/light/dist/assets/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('adminXeria/light/dist/assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('adminXeria/light/dist/assets/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />


    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --umss-blue: #003366;
        --umss-red: #cc0000;
        --light-gray: #f8f9fa;
    }

    body {
        font-family: 'Roboto', sans-serif;
    }

    .hero-section {
        background: linear-gradient(rgba(0, 51, 102, 0.85), rgba(0, 51, 102, 0.95)),
                    url('/api/placeholder/1920/600') center/cover;
        min-height: 80vh;
        display: flex;
        align-items: center;
        color: white;
        position: relative;
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }

    .navbar-custom {
        background-color: var(--umss-blue) !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        padding: 1rem 0;
    }
    
    .btn-umss {
        background-color: var(--umss-blue);
        color: white;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-umss:hover {
        background-color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .btn-hemeroteca {
        background-color: var(--umss-red);
        color: white;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-hemeroteca:hover {
        background-color: #990000;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .feature-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
        background: white;
    }
    
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    .feature-icon {
        height: 80px;
        width: 80px;
        background: var(--light-gray);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .footer-custom {
        background-color: var(--umss-blue);
        color: white;
        padding: 60px 0 30px;
    }

    .nav-link {
        font-weight: 500;
        padding: 10px 15px !important;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #ffcc00 !important;
    }

    .scroll-down {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        animation: bounce 2s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-30px);
        }
        60% {
            transform: translateY(-15px);
        }
    }
    
    </style>

</head>

<body>