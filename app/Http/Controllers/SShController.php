<?php


namespace App\Http\Controllers;


class SShController extends Controller
{
    public function connect (): array
    {
        $ssh2 = ssh2_connect('157.230.54.176', '22');
        ssh2_auth_password ( $ssh2 , 'master_xhuuzsvrtw' , 'ZD~EaMI$IdsFIF7I' );
        $stream = ssh2_exec($ssh2, 'cd applications/nqrusqaujd/public_html && wp plugin list --json');
        stream_set_blocking($stream, true);
        $stream_contents = stream_get_contents($stream);
        /*echo $stream_contents; exit;*/
        $array = json_decode($stream_contents, true);
        return $array;
//        ssh2_disconnect($ssh2);
        return response()->json($array);
    }
}
