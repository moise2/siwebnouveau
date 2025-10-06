<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP-PRPF - Vérification de l'Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 650px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .email-header {
            background-color: #004080;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #ffdd57;
        }
        .email-header h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .email-content {
            padding: 30px;
            line-height: 1.6;
        }
        .email-content p {
            font-size: 1em;
            margin-bottom: 15px;
        }
        .verification-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #004080;
            border-radius: 4px;
            font-size: 1em;
            margin-top: 20px;
            text-align: center;
        }
        .verification-box input {
            font-size: 1.2em;
            padding: 10px;
            width: 100%;
            max-width: 200px;
            margin: 10px auto;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .verification-box button {
            background-color: #004080;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-top: 15px;
        }
        .verification-box button:hover {
            background-color: #003366;
        }
        .email-footer {
            background-color: #004080;
            color: #ffffff;
            padding: 15px;
            text-align: center;
            font-size: 0.9em;
        }
        .signature {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }
        .signature img {
            width: 80px;
            margin-bottom: 8px;
        }
        .signature p {
            margin: 5px 0;
            font-size: 14px;
            color: #333333;
        }
    </style>
</head>
<body>

<div class="email-container">
    <div class="email-header">
        <h2>Vérification de votre Adresse Email</h2>
    </div>

    <div class="email-content">
        <p>Bonjour,</p>
        <p>Merci de vous être abonné au point de presse. Pour compléter votre inscription, veuillez entrer le code de vérification que nous avons envoyé à votre adresse email :</p>
        
        <div class="verification-box">
        <p>Bonjour,</p>
        <p>Veuillez cliquer sur le lien suivant pour vérifier votre adresse email :</p>
        <a href="{{ route('subscriber.verify', ['token' => $verificationToken]) }}">Vérifier mon email</a>
        </div>
    </div>

    <div class="signature">
        <img src="{{ asset('assets/img/armoirie_togo.png') }}" alt="Armoirie du Togo">
        <p style="font-style: italic; color: #777;">République Togolaise</p>
        <p><strong>Secrétariat Permanent pour le suivi des Politiques de Réforme et des Programmes Financiers</strong></p>
    </div>

    <div class="email-footer">
        &copy; {{ date('Y') }} Secrétariat Permanent des Politiques de Réforme, Togo
    </div>
</div>

</body>
</html>
