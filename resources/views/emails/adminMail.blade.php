<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Transaction Notification</title>
</head>
<body>
    <p>Hola Administrador</p>
    <p>El cliente {{$details['client']}} ha realizado varios intentos con fondos insuficientes</p>
    <table>
        <tr>
            <td>ID Transacción</td>
            <td>Monto</td>
            <td>Tipo de Transacción</td>
        </tr>
        @@foreach($details['transactions'] as $transaction)
             <tr>
                 <td>{{$transaction->id}}</td>
                 <td>{{$transaction->amount}}</td>
                 <td>{{($transaction->transaction_type == 'deposit') ? 'Deposito' : 'Retiro'}}</td>
             </tr>
        @@endforeach
    </table>
</body>
</html>
