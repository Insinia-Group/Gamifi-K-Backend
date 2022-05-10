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
get('/rankingById', 'get/rankingsById.php');
get('/rankingsOfModerator', 'get/rankingsOfModerator.php');
get('/tokenValidation', 'get/tokenValidation.php');
get('/history', 'get/history.php');
get('/exist/code/$code', 'get/code.php');
get('/exist/email/$email', 'get/email.php');

/* POSTs */
post('/login', 'post/login.php');
post('/singup', 'post/signup.php');
post('/register', 'post/register.php');
post('/profile/image', 'post/profile-image.php');
post('/profile/data', 'post/profile-data.php');
post('/ranking', 'post/ranking.php');
post('/addRankingByCode', 'post/addRankingByCode.php');
post('/updateData', 'post/updateData.php');
post('/updateInsinia', 'post/updateInsinia.php');
post('/validateEmail', 'post/validateEmail.php');
post('/revertHistory', 'post/revertHistory.php');
post('/deleteRanking', 'post/deleteRanking.php');
post('/exitRanking', 'post/exitRanking.php');
post('/rankingData', 'get/rankingData.php');
post('/addUsersToRanking', 'post/addUsersToRanking.php');
