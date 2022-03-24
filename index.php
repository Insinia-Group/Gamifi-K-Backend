<?php
require_once('class/Router.php');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Expose-Headers: Authorization');

/* GETs */
get('/status', 'get/status.php');
get('/profile', 'get/profile.php');
get('/users', 'get/users.php');
get('/rankings', 'get/rankings.php');
get('/rankingData', 'get/rankingData.php');
get('/rankingById', 'get/rankingById.php');
get('/tokenValidation', 'get/tokenValidation.php');


/* POSTs */
post('/login', 'post/login.php');
post('/singup', 'post/signup.php');
post('/register', 'post/register.php');
post('/profile/image', 'post/profile-image.php');
post('/addRankingByCode', 'post/addRankingByCode.php');