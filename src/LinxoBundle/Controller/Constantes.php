<?php
/**
 * Created by PhpStorm.
 * User: SITRAKA
 * Date: 18/04/2018
 * Time: 14:31
 */

namespace LinxoBundle\Controller;

class Constantes
{
    const REDIRECT_URI = 'http://lesexperts.biz/app/linxo/redirect';
    //const REDIRECT_URI = 'http://newpicdata.fr/app_dev.php/app/linxo/redirect';
    const RESPONSE_TYPE = 'code';
    const SCOPE = 'openid%20accounts_read%20transactions_read%20profile';

    //environnement sand box
    /*const CLIENT_ID  = '2d243b32-dcb7-4362-97fe-3fe82c475e66';
    const CLIENT_SECRET = '4215235f515c443eec7a';
    const BASE_AUTH_URL = 'https://sandbox-auth.linxo.com';
    const BASE_URL = 'https://sandbox-api.linxo.com/v2';*/

    //environnement production
    const CLIENT_ID = 'f5e3d76f-ccc5-4c7e-a169-0da08df6657b';
    const CLIENT_SECRET = '205728b990bd6228c929';
    const BASE_AUTH_URL = 'https://auth.linxo.com';
    const BASE_URL = 'https://api.linxo.com/v2';

    /*login: client1.scriptura@linxo.com
    mot de passe : client1.scriptura@linxo.com*/
}