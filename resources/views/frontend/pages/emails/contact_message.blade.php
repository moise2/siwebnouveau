<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SP-PRPF - Message de Contact</title>
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
        .info-item {
            margin-bottom: 12px;
            font-size: 1em;
            display: flex;
            align-items: center;
        }
        .info-item i {
            margin-right: 10px;
            color: #004080;
        }
        .info-item p {
            margin: 0;
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
        .message-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #004080;
            border-radius: 4px;
            font-size: 1em;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="email-container">
    <!-- En-tête de l'email -->
    <div class="email-header">
        <h2>Nouveau Message de Contact</h2>
    </div>

    <!-- Contenu du message -->
    <div class="email-content">
        <div class="info-item">
            <i class="fas fa-user-circle"></i>
            <p><strong>Nom :</strong> {{ $name }}</p>
        </div>
        <div class="info-item">
            <i class="fas fa-envelope"></i>
            <p><strong>Email :</strong> {{ $email }}</p>
        </div>
        <div class="info-item">
            <i class="fas fa-tag"></i>
            <p><strong>Sujet :</strong> {{ $subject }}</p>
        </div>

        <div class="message-box">
            <h4>Message :</h4>
            <p>{{ $messageContent }}</p>
        </div>
    </div>

    <!-- Signature électronique -->
    <div class="signature">
        <img src="{{ asset('assets/img/armoirie_togo.png') }}" alt="Armoirie du Togo">
        <p style="font-style: italic; color: #777;">République Togolaise</p>
        <p><strong>Secrétariat Permanent pour le suivi des Politiques de Réforme et des Programmes Financiers</strong></p>
    </div>

    <!-- Bas de page de l'email -->
    <div class="email-footer">
        &copy; {{ date('Y') }} Secrétariat Permanent des Politiques de Réforme, Togo
    </div>
</div>

</body>
</html>
