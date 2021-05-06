<!doctype html>
<html lang="ru">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
        <link href="style.css" rel="stylesheet">

        <title>Погода</title>
    </head>
    <body>
        <div class="container">
            <h1>Какая погода?</h1>

            <form method="post">
                <div class="mb-3">
                    <label for="city" class="form-label">Введите название города.</label>
                    <input type="text" class="form-control" name="city" id="city" placeholder="Например, Moscow, Брянск">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>

    </body>
</html>

<?php
$error = "";
$success_msg = "";
$country = "RU";
$mode = "json";
$units = "metric";
$lang = "ru";
$countDay = 7;
$appID = "9305fc634a737017a40cdf5f6aa37e0b";


if ($_POST){
    if (!$_POST['city']){
        $error .= "Необходимо ввести название города<br>";
    }
    else {
        $city = $_POST['city'];
        $url = "http://api.openweathermap.org/data/2.5/forecast?q=$city&cnt=40&lang=$lang&units=$units&appid=$appID";
        //$data = file_get_contents($url);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);

        curl_close($ch);

        if($data){
            $dataJson = json_decode($data);

            $arrayDays = $dataJson->list;
            $min_diff = 100;
            $min_diff_day = "";
            $max_press = -1;

            foreach($arrayDays as $oneDay){
                if ($oneDay->main->pressure > $max_press)
                    $max_press = $oneDay->main->pressure;

                    if ($oneDay->main->temp_max - $oneDay->main->temp_min < $min_diff && $oneDay->main->temp_max - $oneDay->main->temp_min > 0) {
                    $min_diff =$oneDay->main->temp_max - $oneDay->main->temp_min;
                    $min_diff_day = date($oneDay->dt_txt);
                }
            }
            $success_msg .= "<h2>Город: " . $city . "</h2><br>";
            $success_msg .= "<h2>Максимальное давление: " . $max_press . " мм рт. ст.</h2><br>";
            $success_msg .= "<h2>Минимальная разница между ночной и утренней температурой была " . $min_diff_day . " и составляла " . $min_diff . " С</h2><br>";


        }
        else
            $error .= "Сервер недоступен<br>";
    }
    echo $error . $success_msg;
}
?>