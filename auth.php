<?php 
    $app_id='5dea7068f0b04ee58013ddddbd804820'; 

    $url='https://oauth.yandex.ru/authorize?response_type=token&client_id='.$app_id;
    
    if($_POST['confirm-redirect'])
    { 
        header('Location: '.$url);
        exit();      
    }
    
    $token = filter_input( INPUT_GET, 'token');
    
    if (!isset($token))
    {
        $autorized=false;

        echo '<script>
        let token;   

        if (document.location.hash[1]!="null") {
            token = /access_token=([^&]+)/.exec(document.location.hash)[1];
        }  

        if(typeof(token)!="undefined"){
            document.location.href="' . $_SERVER['REQUEST_URI'] . '?token=" + token;
        }
        </script>';
    }
    else
    {
        $autorized=true;
        $ud = curl_init('https://cloud-api.yandex.net/v1/disk/');
       
        curl_setopt($ud, CURLOPT_HTTPHEADER, array('Authorization: OAuth ' . $token));
        curl_setopt($ud, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ud, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ud, CURLOPT_HEADER, false);
        $usr = curl_exec($ud);
        curl_close($ud);

        $usr = json_decode($usr, true);

        if ($usr['used_space']/pow(1024,2)<1024) {
            $usedSpace = round($usr['used_space']/pow(1024,2), 1).' МБ';
        }
        else {
            $usedSpace = round($usr['used_space']/pow(1024,3), 1).' ГБ';
        }

        $totalSpace = round($usr['total_space']/pow(1024,3),1).' ГБ';
        $percSpace = round((($usr['used_space']/$usr['total_space'])*100),2).' %';
    }
