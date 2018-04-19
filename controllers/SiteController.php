<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Kota;
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $this->enableCsrfValidation = false;
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //$kota=Kota::find()->orderBy(['nama_kota'=>SORT_ASC])->all(); 
        $kota = $this->getDataKota();
        return $this->render('index', ['kota'=>$kota]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionTarif(){
        //$kota=Kota::find()->orderBy(['nama_kota'=>SORT_ASC])->all();
        $kota = $this->getDataKota();
        //get params post
        $params = Yii::$app->request->post();
        if (empty($params)) {
            return $this->render('index', ['kota'=>$kota]);
        }

        //var_dump($params); die();

        //check params filters
        if(!isset($params['filters']) || empty($params['filters'])) {
            return $this->render('index', 
                ['alert'=>"Harap centang salah satu filter", 
                'kota'=>$kota]
            );
        }
        $filters = $params['filters'];

        $data = array(
                        array('ekspedisi' => "jne", 'jenis' => "reg", 'tarif' => 15000, 'durasi' => 3),
                        array('ekspedisi' => "tiki", 'jenis' => "reg", 'tarif' => 14000, 'durasi' => 2),
                        array('ekspedisi' => "pos", 'jenis' => "reg", 'tarif' => 15150, 'durasi' => 3),
                        array('ekspedisi' => "j&t", 'jenis' => "ez", 'tarif' => 16000, 'durasi' => 3),
                        array('ekspedisi' => "pandu logistik", 'jenis' => "reg", 'tarif' => 22500, 'durasi' => 2),
                        array('ekspedisi' => "esl express", 'jenis' => "rdx", 'tarif' => 18200, 'durasi' => 3),
                        array('ekspedisi' => "rosalia", 'jenis' => "reg", 'tarif' => 14300, 'durasi' => 3),
                        array('ekspedisi' => "wahana", 'jenis' => "reg", 'tarif' => 7000, 'durasi' => 3),
                        array('ekspedisi' => "sicepat", 'jenis' => "reg", 'tarif' => 15000, 'durasi' => 3),
                        array('ekspedisi' => "first logistik", 'jenis' => "reg", 'tarif' => 11000, 'durasi' => 3)
                    );
        $dataMurah = array();
        $dataSedang = array();
        $dataMahal = array();

        //n = jumlah data
        $n = sizeof($data);
        //init tarif maximal dengan nilai 0
        $tarifMax = 0;
        $durasiMax = 0;
        //looping untuk mencari nilai tarifMax
        for ($i=0; $i < $n; $i++) { 
            if($data[$i]['tarif'] > $tarifMax) {
                $tarifMax = $data[$i]['tarif'];
            }
            if($data[$i]['durasi'] > $durasiMax) {
                $durasiMax = $data[$i]['durasi'];
            }
        }

        //init nilai a, b, dan c
        $a = $tarifMax / 4;
        $b = $tarifMax / 2;
        $c = (3 * $tarifMax) / 4;
        $a1 = 1; $b1 = 2; $c1 = 3;

        //hitung derajat keanggotaan tarif (µ[x])
        //lalu yang murah akan dimasukkan ke array $dataMurah, dst
        for ($i=0; $i < $n; $i++) { 

            //$tarif = tarif yang ada pada data index ke $i
            $tarif = $data[$i]['tarif'];
            
            // hitung nilai murah berdasarkan rumus
            if ($tarif <= $a){
                $murah = 1;
            } elseif ($a <= $tarif && $tarif < $b) {
                $murah = ($b - $tarif) / ($b - $a);
            } elseif ($tarif >= $b) {
                $murah = 0;
            }
            //hitung nilai sedang berdasarkan rumus
            if ($tarif <= $a || $tarif >= $c){
                $sedang = 0;
            } elseif ($a <= $tarif && $tarif < $b) {
                $sedang = ($tarif - $a)/($b - $a);
            } elseif ($b <= $tarif && $tarif < $c){
                $sedang = ($c - $tarif)/($c - $b);
            }
            //hitung nilai mahal berdasarkan rumus
            if ($tarif <= $b){
                $mahal = 0;
            } elseif ($b <= $tarif && $tarif < $c) {
                $mahal = ($tarif - $b)/($c - $b);
            } elseif ($tarif >= $c){
                $mahal = 1;
            }

            //hitung nilai durasi lama
            $durasi = $data[$i]['durasi'];
            if($durasi <= $a1) {
                $lama = 1;
            } elseif ($a1 <= $durasi && $durasi < $b1) {
                $lama = ($b - $durasi) / ($b1 - $a1);
            } elseif ($durasi >= $b1) {
                $lama = 0;
            }
            //hitung nilai durasi sedang
            if ($durasi <= $a1 || $durasi >= $c1){
                $sedang = 0;
            } elseif ($a1 <= $durasi && $durasi < $b1) {
                $sedang = ($durasi - $a1)/($b1 - $a1);
            } elseif ($b1 <= $durasi && $durasi < $c1){
                $sedang = ($c1 - $durasi)/($c1 - $b1);
            }
            //hitung nilai durasi cepat
            if ($durasi <= $b1){
                $cepat = 0;
            } elseif ($b1 <= $durasi && $durasi < $c1) {
                $cepat = ($durasi - $b1)/($c1 - $b1);
            } elseif ($durasi >= $c1){
                $cepat = 1;
            }
            
            //masukkan hasil perhitungan tarif
            if ($murah >= $sedang && $murah >= $mahal) {
                $data[$i]['tarif_murah'] = $murah;
            }
            if ($sedang >= $mahal && $sedang >= $murah) {
                $data[$i]['tarif_sedang'] = $sedang;
            }
            if ($mahal >= $murah && $mahal >= $sedang) {
                $data[$i]['tarif_mahal'] = $mahal;
            }

            //masukkan hasil perhitungan durasi
            if ($lama > 0) {
                $data[$i]['durasi_lama'] = $lama;
            }
            if ($sedang > 0) {
                $data[$i]['durasi_sedang'] = $sedang;
            }
            if ($cepat > 0) {
                $data[$i]['durasi_cepat'] = $cepat;
            }
        }


        $filtered = array();
        for ($i=0; $i < $n; $i++) {
            //jika hanya filter salah satu
            if (sizeof($filters) == 1) {
                //jika hanya memfilter tarif
                if ($filters[0] == "tarif") {
                    if ( ($params['tarif'] == "murah" && isset($data[$i]["tarif_murah"])) ||
                            ( ($params['tarif'] == "sedang" && isset($data[$i]["tarif_sedang"])) ||
                                ($params['tarif'] == "mahal" && isset($data[$i]["tarif_mahal"])) ) ) {
                        array_push($filtered, $data[$i]);
                    }
                }
                //jika hanya memfilter durasi
                else {
                    if ( ($params['durasi'] == "lama" && isset($data[$i]["durasi_lama"])) ||
                            ( ($params['durasi'] == "sedang" && isset($data[$i]["durasi_sedang"])) ||
                                ($params['durasi'] == "cepat" && isset($data[$i]["durasi_cepat"])) ) ) {
                        array_push($filtered, $data[$i]);
                    }
                }
            } else {
                if (($params['tarif'] == "murah" && isset($data[$i]["tarif_murah"])) &&
                        ( ($params['durasi'] == "lama" && isset($data[$i]["durasi_lama"])) ||
                            ( ($params['durasi'] == "sedang" && isset($data[$i]["durasi_sedang"])) ||
                                ($params['durasi'] == "cepat" && isset($data[$i]["durasi_cepat"])) ) )) {
                    array_push($filtered, $data[$i]);
                }
                elseif (($params['tarif'] == "sedang" && isset($data[$i]["tarif_sedang"])) &&
                        ( ($params['durasi'] == "lama" && isset($data[$i]["durasi_lama"])) ||
                            ( ($params['durasi'] == "sedang" && isset($data[$i]["durasi_sedang"])) ||
                                ($params['durasi'] == "cepat" && isset($data[$i]["durasi_cepat"])) ) )) {
                    array_push($filtered, $data[$i]);
                } elseif (($params['tarif'] == "mahal" && isset($data[$i]["tarif_mahal"])) &&
                        ( ($params['durasi'] == "lama" && isset($data[$i]["durasi_lama"])) ||
                            ( ($params['durasi'] == "sedang" && isset($data[$i]["durasi_sedang"])) ||
                                ($params['durasi'] == "cepat" && isset($data[$i]["durasi_cepat"])) ) )) {
                    array_push($filtered, $data[$i]);
                }
            }
        }
            
        return $this->render('index', 
            ['fuzzy'=>$filtered, 
            'kota'=>$kota]
        );
    }


    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function getDataKota()
    {
        return array(
            array("city_id" => "1", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Barat", "postal_code" => "23681"),
      array("city_id" => "2", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Barat Daya", "postal_code" => "23764"),
      array("city_id" => "3", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Besar", "postal_code" => "23951"),
      array("city_id" => "4", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Jaya", "postal_code" => "23654"),
      array("city_id" => "5", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Selatan", "postal_code" => "23719"),
      array("city_id" => "6", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Singkil", "postal_code" => "24785"),
      array("city_id" => "7", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Tamiang", "postal_code" => "24476"),
      array("city_id" => "8", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Tengah", "postal_code" => "24511"),
      array("city_id" => "9", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Tenggara", "postal_code" => "24611"),
      array("city_id" => "10", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Timur", "postal_code" => "24454"),
      array("city_id" => "11", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Aceh Utara", "postal_code" => "24382"),
      array("city_id" => "12", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Agam", "postal_code" => "26411"),
      array("city_id" => "13", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Alor", "postal_code" => "85811"),
      array("city_id" => "14", "province_id" => "19", "province" => "Maluku", "type" => "Kota", "city_name" => "Ambon", "postal_code" => "97222"),
      array("city_id" => "15", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Asahan", "postal_code" => "21214"),
      array("city_id" => "16", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Asmat", "postal_code" => "99777"),
      array("city_id" => "17", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Badung", "postal_code" => "80351"),
      array("city_id" => "18", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Balangan", "postal_code" => "71611"),
      array("city_id" => "19", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kota", "city_name" => "Balikpapan", "postal_code" => "76111"),
      array("city_id" => "20", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kota", "city_name" => "Banda Aceh", "postal_code" => "23238"),
      array("city_id" => "21", "province_id" => "18", "province" => "Lampung", "type" => "Kota", "city_name" => "Bandar Lampung", "postal_code" => "35139"),
      array("city_id" => "22", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Bandung", "postal_code" => "40311"),
      array("city_id" => "23", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Bandung", "postal_code" => "40111"),
      array("city_id" => "24", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Bandung Barat", "postal_code" => "40721"),
      array("city_id" => "25", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Banggai", "postal_code" => "94711"),
      array("city_id" => "26", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Banggai Kepulauan", "postal_code" => "94881"),
      array("city_id" => "27", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Bangka", "postal_code" => "33212"),
      array("city_id" => "28", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Bangka Barat", "postal_code" => "33315"),
      array("city_id" => "29", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Bangka Selatan", "postal_code" => "33719"),
      array("city_id" => "30", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Bangka Tengah", "postal_code" => "33613"),
      array("city_id" => "31", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Bangkalan", "postal_code" => "69118"),
      array("city_id" => "32", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Bangli", "postal_code" => "80619"),
      array("city_id" => "33", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Banjar", "postal_code" => "70619"),
      array("city_id" => "34", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Banjar", "postal_code" => "46311"),
      array("city_id" => "35", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kota", "city_name" => "Banjarbaru", "postal_code" => "70712"),
      array("city_id" => "36", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kota", "city_name" => "Banjarmasin", "postal_code" => "70117"),
      array("city_id" => "37", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Banjarnegara", "postal_code" => "53419"),
      array("city_id" => "38", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Bantaeng", "postal_code" => "92411"),
      array("city_id" => "39", "province_id" => "5", "province" => "DI Yogyakarta", "type" => "Kabupaten", "city_name" => "Bantul", "postal_code" => "55715"),
      array("city_id" => "40", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Banyuasin", "postal_code" => "30911"),
      array("city_id" => "41", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Banyumas", "postal_code" => "53114"),
      array("city_id" => "42", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Banyuwangi", "postal_code" => "68416"),
      array("city_id" => "43", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Barito Kuala", "postal_code" => "70511"),
      array("city_id" => "44", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Barito Selatan", "postal_code" => "73711"),
      array("city_id" => "45", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Barito Timur", "postal_code" => "73671"),
      array("city_id" => "46", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Barito Utara", "postal_code" => "73881"),
      array("city_id" => "47", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Barru", "postal_code" => "90719"),
      array("city_id" => "48", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kota", "city_name" => "Batam", "postal_code" => "29413"),
      array("city_id" => "49", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Batang", "postal_code" => "51211"),
      array("city_id" => "50", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Batang Hari", "postal_code" => "36613"),
      array("city_id" => "51", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Batu", "postal_code" => "65311"),
      array("city_id" => "52", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Batu Bara", "postal_code" => "21655"),
      array("city_id" => "53", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kota", "city_name" => "Bau-Bau", "postal_code" => "93719"),
      array("city_id" => "54", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Bekasi", "postal_code" => "17837"),
      array("city_id" => "55", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Bekasi", "postal_code" => "17121"),
      array("city_id" => "56", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Belitung", "postal_code" => "33419"),
      array("city_id" => "57", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kabupaten", "city_name" => "Belitung Timur", "postal_code" => "33519"),
      array("city_id" => "58", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Belu", "postal_code" => "85711"),
      array("city_id" => "59", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Bener Meriah", "postal_code" => "24581"),
      array("city_id" => "60", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Bengkalis", "postal_code" => "28719"),
      array("city_id" => "61", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Bengkayang", "postal_code" => "79213"),
      array("city_id" => "62", "province_id" => "4", "province" => "Bengkulu", "type" => "Kota", "city_name" => "Bengkulu", "postal_code" => "38229"),
      array("city_id" => "63", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Bengkulu Selatan", "postal_code" => "38519"),
      array("city_id" => "64", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Bengkulu Tengah", "postal_code" => "38319"),
      array("city_id" => "65", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Bengkulu Utara", "postal_code" => "38619"),
      array("city_id" => "66", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Berau", "postal_code" => "77311"),
      array("city_id" => "67", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Biak Numfor", "postal_code" => "98119"),
      array("city_id" => "68", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Bima", "postal_code" => "84171"),
      array("city_id" => "69", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kota", "city_name" => "Bima", "postal_code" => "84139"),
      array("city_id" => "70", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Binjai", "postal_code" => "20712"),
      array("city_id" => "71", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kabupaten", "city_name" => "Bintan", "postal_code" => "29135"),
      array("city_id" => "72", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Bireuen", "postal_code" => "24219"),
      array("city_id" => "73", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kota", "city_name" => "Bitung", "postal_code" => "95512"),
      array("city_id" => "74", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Blitar", "postal_code" => "66171"),
      array("city_id" => "75", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Blitar", "postal_code" => "66124"),
      array("city_id" => "76", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Blora", "postal_code" => "58219"),
      array("city_id" => "77", "province_id" => "7", "province" => "Gorontalo", "type" => "Kabupaten", "city_name" => "Boalemo", "postal_code" => "96319"),
      array("city_id" => "78", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Bogor", "postal_code" => "16911"),
      array("city_id" => "79", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Bogor", "postal_code" => "16119"),
      array("city_id" => "80", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Bojonegoro", "postal_code" => "62119"),
      array("city_id" => "81", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Bolaang Mongondow (Bolmong)", "postal_code" => "95755"),
      array("city_id" => "82", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Bolaang Mongondow Selatan", "postal_code" => "95774"),
      array("city_id" => "83", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Bolaang Mongondow Timur", "postal_code" => "95783"),
      array("city_id" => "84", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Bolaang Mongondow Utara", "postal_code" => "95765"),
      array("city_id" => "85", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Bombana", "postal_code" => "93771"),
      array("city_id" => "86", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Bondowoso", "postal_code" => "68219"),
      array("city_id" => "87", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Bone", "postal_code" => "92713"),
      array("city_id" => "88", "province_id" => "7", "province" => "Gorontalo", "type" => "Kabupaten", "city_name" => "Bone Bolango", "postal_code" => "96511"),
      array("city_id" => "89", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kota", "city_name" => "Bontang", "postal_code" => "75313"),
      array("city_id" => "90", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Boven Digoel", "postal_code" => "99662"),
      array("city_id" => "91", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Boyolali", "postal_code" => "57312"),
      array("city_id" => "92", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Brebes", "postal_code" => "52212"),
      array("city_id" => "93", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Bukittinggi", "postal_code" => "26115"),
      array("city_id" => "94", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Buleleng", "postal_code" => "81111"),
      array("city_id" => "95", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Bulukumba", "postal_code" => "92511"),
      array("city_id" => "96", "province_id" => "16", "province" => "Kalimantan Utara", "type" => "Kabupaten", "city_name" => "Bulungan (Bulongan)", "postal_code" => "77211"),
      array("city_id" => "97", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Bungo", "postal_code" => "37216"),
      array("city_id" => "98", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Buol", "postal_code" => "94564"),
      array("city_id" => "99", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Buru", "postal_code" => "97371"),
      array("city_id" => "100", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Buru Selatan", "postal_code" => "97351"),
      array("city_id" => "101", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Buton", "postal_code" => "93754"),
      array("city_id" => "102", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Buton Utara", "postal_code" => "93745"),
      array("city_id" => "103", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Ciamis", "postal_code" => "46211"),
      array("city_id" => "104", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Cianjur", "postal_code" => "43217"),
      array("city_id" => "105", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Cilacap", "postal_code" => "53211"),
      array("city_id" => "106", "province_id" => "3", "province" => "Banten", "type" => "Kota", "city_name" => "Cilegon", "postal_code" => "42417"),
      array("city_id" => "107", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Cimahi", "postal_code" => "40512"),
      array("city_id" => "108", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Cirebon", "postal_code" => "45611"),
      array("city_id" => "109", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Cirebon", "postal_code" => "45116"),
      array("city_id" => "110", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Dairi", "postal_code" => "22211"),
      array("city_id" => "111", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Deiyai (Deliyai)", "postal_code" => "98784"),
      array("city_id" => "112", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Deli Serdang", "postal_code" => "20511"),
      array("city_id" => "113", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Demak", "postal_code" => "59519"),
      array("city_id" => "114", "province_id" => "1", "province" => "Bali", "type" => "Kota", "city_name" => "Denpasar", "postal_code" => "80227"),
      array("city_id" => "115", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Depok", "postal_code" => "16416"),
      array("city_id" => "116", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Dharmasraya", "postal_code" => "27612"),
      array("city_id" => "117", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Dogiyai", "postal_code" => "98866"),
      array("city_id" => "118", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Dompu", "postal_code" => "84217"),
      array("city_id" => "119", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Donggala", "postal_code" => "94341"),
      array("city_id" => "120", "province_id" => "26", "province" => "Riau", "type" => "Kota", "city_name" => "Dumai", "postal_code" => "28811"),
      array("city_id" => "121", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Empat Lawang", "postal_code" => "31811"),
      array("city_id" => "122", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Ende", "postal_code" => "86351"),
      array("city_id" => "123", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Enrekang", "postal_code" => "91719"),
      array("city_id" => "124", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Fakfak", "postal_code" => "98651"),
      array("city_id" => "125", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Flores Timur", "postal_code" => "86213"),
      array("city_id" => "126", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Garut", "postal_code" => "44126"),
      array("city_id" => "127", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Gayo Lues", "postal_code" => "24653"),
      array("city_id" => "128", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Gianyar", "postal_code" => "80519"),
      array("city_id" => "129", "province_id" => "7", "province" => "Gorontalo", "type" => "Kabupaten", "city_name" => "Gorontalo", "postal_code" => "96218"),
      array("city_id" => "130", "province_id" => "7", "province" => "Gorontalo", "type" => "Kota", "city_name" => "Gorontalo", "postal_code" => "96115"),
      array("city_id" => "131", "province_id" => "7", "province" => "Gorontalo", "type" => "Kabupaten", "city_name" => "Gorontalo Utara", "postal_code" => "96611"),
      array("city_id" => "132", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Gowa", "postal_code" => "92111"),
      array("city_id" => "133", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Gresik", "postal_code" => "61115"),
      array("city_id" => "134", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Grobogan", "postal_code" => "58111"),
      array("city_id" => "135", "province_id" => "5", "province" => "DI Yogyakarta", "type" => "Kabupaten", "city_name" => "Gunung Kidul", "postal_code" => "55812"),
      array("city_id" => "136", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Gunung Mas", "postal_code" => "74511"),
      array("city_id" => "137", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Gunungsitoli", "postal_code" => "22813"),
      array("city_id" => "138", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Halmahera Barat", "postal_code" => "97757"),
      array("city_id" => "139", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Halmahera Selatan", "postal_code" => "97911"),
      array("city_id" => "140", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Halmahera Tengah", "postal_code" => "97853"),
      array("city_id" => "141", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Halmahera Timur", "postal_code" => "97862"),
      array("city_id" => "142", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Halmahera Utara", "postal_code" => "97762"),
      array("city_id" => "143", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Hulu Sungai Selatan", "postal_code" => "71212"),
      array("city_id" => "144", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Hulu Sungai Tengah", "postal_code" => "71313"),
      array("city_id" => "145", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Hulu Sungai Utara", "postal_code" => "71419"),
      array("city_id" => "146", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Humbang Hasundutan", "postal_code" => "22457"),
      array("city_id" => "147", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Indragiri Hilir", "postal_code" => "29212"),
      array("city_id" => "148", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Indragiri Hulu", "postal_code" => "29319"),
      array("city_id" => "149", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Indramayu", "postal_code" => "45214"),
      array("city_id" => "150", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Intan Jaya", "postal_code" => "98771"),
      array("city_id" => "151", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kota", "city_name" => "Jakarta Barat", "postal_code" => "11220"),
      array("city_id" => "152", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kota", "city_name" => "Jakarta Pusat", "postal_code" => "10540"),
      array("city_id" => "153", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kota", "city_name" => "Jakarta Selatan", "postal_code" => "12230"),
      array("city_id" => "154", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kota", "city_name" => "Jakarta Timur", "postal_code" => "13330"),
      array("city_id" => "155", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kota", "city_name" => "Jakarta Utara", "postal_code" => "14140"),
      array("city_id" => "156", "province_id" => "8", "province" => "Jambi", "type" => "Kota", "city_name" => "Jambi", "postal_code" => "36111"),
      array("city_id" => "157", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Jayapura", "postal_code" => "99352"),
      array("city_id" => "158", "province_id" => "24", "province" => "Papua", "type" => "Kota", "city_name" => "Jayapura", "postal_code" => "99114"),
      array("city_id" => "159", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Jayawijaya", "postal_code" => "99511"),
      array("city_id" => "160", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Jember", "postal_code" => "68113"),
      array("city_id" => "161", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Jembrana", "postal_code" => "82251"),
      array("city_id" => "162", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Jeneponto", "postal_code" => "92319"),
      array("city_id" => "163", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Jepara", "postal_code" => "59419"),
      array("city_id" => "164", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Jombang", "postal_code" => "61415"),
      array("city_id" => "165", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Kaimana", "postal_code" => "98671"),
      array("city_id" => "166", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Kampar", "postal_code" => "28411"),
      array("city_id" => "167", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Kapuas", "postal_code" => "73583"),
      array("city_id" => "168", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Kapuas Hulu", "postal_code" => "78719"),
      array("city_id" => "169", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Karanganyar", "postal_code" => "57718"),
      array("city_id" => "170", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Karangasem", "postal_code" => "80819"),
      array("city_id" => "171", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Karawang", "postal_code" => "41311"),
      array("city_id" => "172", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kabupaten", "city_name" => "Karimun", "postal_code" => "29611"),
      array("city_id" => "173", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Karo", "postal_code" => "22119"),
      array("city_id" => "174", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Katingan", "postal_code" => "74411"),
      array("city_id" => "175", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Kaur", "postal_code" => "38911"),
      array("city_id" => "176", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Kayong Utara", "postal_code" => "78852"),
      array("city_id" => "177", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Kebumen", "postal_code" => "54319"),
      array("city_id" => "178", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Kediri", "postal_code" => "64184"),
      array("city_id" => "179", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Kediri", "postal_code" => "64125"),
      array("city_id" => "180", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Keerom", "postal_code" => "99461"),
      array("city_id" => "181", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Kendal", "postal_code" => "51314"),
      array("city_id" => "182", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kota", "city_name" => "Kendari", "postal_code" => "93126"),
      array("city_id" => "183", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Kepahiang", "postal_code" => "39319"),
      array("city_id" => "184", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kabupaten", "city_name" => "Kepulauan Anambas", "postal_code" => "29991"),
      array("city_id" => "185", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Kepulauan Aru", "postal_code" => "97681"),
      array("city_id" => "186", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Kepulauan Mentawai", "postal_code" => "25771"),
      array("city_id" => "187", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Kepulauan Meranti", "postal_code" => "28791"),
      array("city_id" => "188", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Kepulauan Sangihe", "postal_code" => "95819"),
      array("city_id" => "189", "province_id" => "6", "province" => "DKI Jakarta", "type" => "Kabupaten", "city_name" => "Kepulauan Seribu", "postal_code" => "14550"),
      array("city_id" => "190", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Kepulauan Siau Tagulandang Biaro (Sitaro)", "postal_code" => "95862"),
      array("city_id" => "191", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Kepulauan Sula", "postal_code" => "97995"),
      array("city_id" => "192", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Kepulauan Talaud", "postal_code" => "95885"),
      array("city_id" => "193", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Kepulauan Yapen (Yapen Waropen)", "postal_code" => "98211"),
      array("city_id" => "194", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Kerinci", "postal_code" => "37167"),
      array("city_id" => "195", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Ketapang", "postal_code" => "78874"),
      array("city_id" => "196", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Klaten", "postal_code" => "57411"),
      array("city_id" => "197", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Klungkung", "postal_code" => "80719"),
      array("city_id" => "198", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Kolaka", "postal_code" => "93511"),
      array("city_id" => "199", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Kolaka Utara", "postal_code" => "93911"),
      array("city_id" => "200", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Konawe", "postal_code" => "93411"),
      array("city_id" => "201", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Konawe Selatan", "postal_code" => "93811"),
      array("city_id" => "202", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Konawe Utara", "postal_code" => "93311"),
      array("city_id" => "203", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Kotabaru", "postal_code" => "72119"),
      array("city_id" => "204", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kota", "city_name" => "Kotamobagu", "postal_code" => "95711"),
      array("city_id" => "205", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Kotawaringin Barat", "postal_code" => "74119"),
      array("city_id" => "206", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Kotawaringin Timur", "postal_code" => "74364"),
      array("city_id" => "207", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Kuantan Singingi", "postal_code" => "29519"),
      array("city_id" => "208", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Kubu Raya", "postal_code" => "78311"),
      array("city_id" => "209", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Kudus", "postal_code" => "59311"),
      array("city_id" => "210", "province_id" => "5", "province" => "DI Yogyakarta", "type" => "Kabupaten", "city_name" => "Kulon Progo", "postal_code" => "55611"),
      array("city_id" => "211", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Kuningan", "postal_code" => "45511"),
      array("city_id" => "212", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Kupang", "postal_code" => "85362"),
      array("city_id" => "213", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kota", "city_name" => "Kupang", "postal_code" => "85119"),
      array("city_id" => "214", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Kutai Barat", "postal_code" => "75711"),
      array("city_id" => "215", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Kutai Kartanegara", "postal_code" => "75511"),
      array("city_id" => "216", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Kutai Timur", "postal_code" => "75611"),
      array("city_id" => "217", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Labuhan Batu", "postal_code" => "21412"),
      array("city_id" => "218", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Labuhan Batu Selatan", "postal_code" => "21511"),
      array("city_id" => "219", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Labuhan Batu Utara", "postal_code" => "21711"),
      array("city_id" => "220", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Lahat", "postal_code" => "31419"),
      array("city_id" => "221", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Lamandau", "postal_code" => "74611"),
      array("city_id" => "222", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Lamongan", "postal_code" => "64125"),
      array("city_id" => "223", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Lampung Barat", "postal_code" => "34814"),
      array("city_id" => "224", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Lampung Selatan", "postal_code" => "35511"),
      array("city_id" => "225", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Lampung Tengah", "postal_code" => "34212"),
      array("city_id" => "226", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Lampung Timur", "postal_code" => "34319"),
      array("city_id" => "227", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Lampung Utara", "postal_code" => "34516"),
      array("city_id" => "228", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Landak", "postal_code" => "78319"),
      array("city_id" => "229", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Langkat", "postal_code" => "20811"),
      array("city_id" => "230", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kota", "city_name" => "Langsa", "postal_code" => "24412"),
      array("city_id" => "231", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Lanny Jaya", "postal_code" => "99531"),
      array("city_id" => "232", "province_id" => "3", "province" => "Banten", "type" => "Kabupaten", "city_name" => "Lebak", "postal_code" => "42319"),
      array("city_id" => "233", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Lebong", "postal_code" => "39264"),
      array("city_id" => "234", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Lembata", "postal_code" => "86611"),
      array("city_id" => "235", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kota", "city_name" => "Lhokseumawe", "postal_code" => "24352"),
      array("city_id" => "236", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Lima Puluh Koto/Kota", "postal_code" => "26671"),
      array("city_id" => "237", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kabupaten", "city_name" => "Lingga", "postal_code" => "29811"),
      array("city_id" => "238", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Lombok Barat", "postal_code" => "83311"),
      array("city_id" => "239", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Lombok Tengah", "postal_code" => "83511"),
      array("city_id" => "240", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Lombok Timur", "postal_code" => "83612"),
      array("city_id" => "241", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Lombok Utara", "postal_code" => "83711"),
      array("city_id" => "242", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kota", "city_name" => "Lubuk Linggau", "postal_code" => "31614"),
      array("city_id" => "243", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Lumajang", "postal_code" => "67319"),
      array("city_id" => "244", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Luwu", "postal_code" => "91994"),
      array("city_id" => "245", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Luwu Timur", "postal_code" => "92981"),
      array("city_id" => "246", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Luwu Utara", "postal_code" => "92911"),
      array("city_id" => "247", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Madiun", "postal_code" => "63153"),
      array("city_id" => "248", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Madiun", "postal_code" => "63122"),
      array("city_id" => "249", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Magelang", "postal_code" => "56519"),
      array("city_id" => "250", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Magelang", "postal_code" => "56133"),
      array("city_id" => "251", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Magetan", "postal_code" => "63314"),
      array("city_id" => "252", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Majalengka", "postal_code" => "45412"),
      array("city_id" => "253", "province_id" => "27", "province" => "Sulawesi Barat", "type" => "Kabupaten", "city_name" => "Majene", "postal_code" => "91411"),
      array("city_id" => "254", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kota", "city_name" => "Makassar", "postal_code" => "90111"),
      array("city_id" => "255", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Malang", "postal_code" => "65163"),
      array("city_id" => "256", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Malang", "postal_code" => "65112"),
      array("city_id" => "257", "province_id" => "16", "province" => "Kalimantan Utara", "type" => "Kabupaten", "city_name" => "Malinau", "postal_code" => "77511"),
      array("city_id" => "258", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Maluku Barat Daya", "postal_code" => "97451"),
      array("city_id" => "259", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Maluku Tengah", "postal_code" => "97513"),
      array("city_id" => "260", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Maluku Tenggara", "postal_code" => "97651"),
      array("city_id" => "261", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Maluku Tenggara Barat", "postal_code" => "97465"),
      array("city_id" => "262", "province_id" => "27", "province" => "Sulawesi Barat", "type" => "Kabupaten", "city_name" => "Mamasa", "postal_code" => "91362"),
      array("city_id" => "263", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Mamberamo Raya", "postal_code" => "99381"),
      array("city_id" => "264", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Mamberamo Tengah", "postal_code" => "99553"),
      array("city_id" => "265", "province_id" => "27", "province" => "Sulawesi Barat", "type" => "Kabupaten", "city_name" => "Mamuju", "postal_code" => "91519"),
      array("city_id" => "266", "province_id" => "27", "province" => "Sulawesi Barat", "type" => "Kabupaten", "city_name" => "Mamuju Utara", "postal_code" => "91571"),
      array("city_id" => "267", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kota", "city_name" => "Manado", "postal_code" => "95247"),
      array("city_id" => "268", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Mandailing Natal", "postal_code" => "22916"),
      array("city_id" => "269", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Manggarai", "postal_code" => "86551"),
      array("city_id" => "270", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Manggarai Barat", "postal_code" => "86711"),
      array("city_id" => "271", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Manggarai Timur", "postal_code" => "86811"),
      array("city_id" => "272", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Manokwari", "postal_code" => "98311"),
      array("city_id" => "273", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Manokwari Selatan", "postal_code" => "98355"),
      array("city_id" => "274", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Mappi", "postal_code" => "99853"),
      array("city_id" => "275", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Maros", "postal_code" => "90511"),
      array("city_id" => "276", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kota", "city_name" => "Mataram", "postal_code" => "83131"),
      array("city_id" => "277", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Maybrat", "postal_code" => "98051"),
      array("city_id" => "278", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Medan", "postal_code" => "20228"),
      array("city_id" => "279", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Melawi", "postal_code" => "78619"),
      array("city_id" => "280", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Merangin", "postal_code" => "37319"),
      array("city_id" => "281", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Merauke", "postal_code" => "99613"),
      array("city_id" => "282", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Mesuji", "postal_code" => "34911"),
      array("city_id" => "283", "province_id" => "18", "province" => "Lampung", "type" => "Kota", "city_name" => "Metro", "postal_code" => "34111"),
      array("city_id" => "284", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Mimika", "postal_code" => "99962"),
      array("city_id" => "285", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Minahasa", "postal_code" => "95614"),
      array("city_id" => "286", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Minahasa Selatan", "postal_code" => "95914"),
      array("city_id" => "287", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Minahasa Tenggara", "postal_code" => "95995"),
      array("city_id" => "288", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kabupaten", "city_name" => "Minahasa Utara", "postal_code" => "95316"),
      array("city_id" => "289", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Mojokerto", "postal_code" => "61382"),
      array("city_id" => "290", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Mojokerto", "postal_code" => "61316"),
      array("city_id" => "291", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Morowali", "postal_code" => "94911"),
      array("city_id" => "292", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Muara Enim", "postal_code" => "31315"),
      array("city_id" => "293", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Muaro Jambi", "postal_code" => "36311"),
      array("city_id" => "294", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Muko Muko", "postal_code" => "38715"),
      array("city_id" => "295", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Muna", "postal_code" => "93611"),
      array("city_id" => "296", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Murung Raya", "postal_code" => "73911"),
      array("city_id" => "297", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Musi Banyuasin", "postal_code" => "30719"),
      array("city_id" => "298", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Musi Rawas", "postal_code" => "31661"),
      array("city_id" => "299", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Nabire", "postal_code" => "98816"),
      array("city_id" => "300", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Nagan Raya", "postal_code" => "23674"),
      array("city_id" => "301", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Nagekeo", "postal_code" => "86911"),
      array("city_id" => "302", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kabupaten", "city_name" => "Natuna", "postal_code" => "29711"),
      array("city_id" => "303", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Nduga", "postal_code" => "99541"),
      array("city_id" => "304", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Ngada", "postal_code" => "86413"),
      array("city_id" => "305", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Nganjuk", "postal_code" => "64414"),
      array("city_id" => "306", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Ngawi", "postal_code" => "63219"),
      array("city_id" => "307", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Nias", "postal_code" => "22876"),
      array("city_id" => "308", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Nias Barat", "postal_code" => "22895"),
      array("city_id" => "309", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Nias Selatan", "postal_code" => "22865"),
      array("city_id" => "310", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Nias Utara", "postal_code" => "22856"),
      array("city_id" => "311", "province_id" => "16", "province" => "Kalimantan Utara", "type" => "Kabupaten", "city_name" => "Nunukan", "postal_code" => "77421"),
      array("city_id" => "312", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Ogan Ilir", "postal_code" => "30811"),
      array("city_id" => "313", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Ogan Komering Ilir", "postal_code" => "30618"),
      array("city_id" => "314", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Ogan Komering Ulu", "postal_code" => "32112"),
      array("city_id" => "315", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Ogan Komering Ulu Selatan", "postal_code" => "32211"),
      array("city_id" => "316", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kabupaten", "city_name" => "Ogan Komering Ulu Timur", "postal_code" => "32312"),
      array("city_id" => "317", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Pacitan", "postal_code" => "63512"),
      array("city_id" => "318", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Padang", "postal_code" => "25112"),
      array("city_id" => "319", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Padang Lawas", "postal_code" => "22763"),
      array("city_id" => "320", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Padang Lawas Utara", "postal_code" => "22753"),
      array("city_id" => "321", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Padang Panjang", "postal_code" => "27122"),
      array("city_id" => "322", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Padang Pariaman", "postal_code" => "25583"),
      array("city_id" => "323", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Padang Sidempuan", "postal_code" => "22727"),
      array("city_id" => "324", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kota", "city_name" => "Pagar Alam", "postal_code" => "31512"),
      array("city_id" => "325", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Pakpak Bharat", "postal_code" => "22272"),
      array("city_id" => "326", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kota", "city_name" => "Palangka Raya", "postal_code" => "73112"),
      array("city_id" => "327", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kota", "city_name" => "Palembang", "postal_code" => "31512"),
      array("city_id" => "328", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kota", "city_name" => "Palopo", "postal_code" => "91911"),
      array("city_id" => "329", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kota", "city_name" => "Palu", "postal_code" => "94111"),
      array("city_id" => "330", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Pamekasan", "postal_code" => "69319"),
      array("city_id" => "331", "province_id" => "3", "province" => "Banten", "type" => "Kabupaten", "city_name" => "Pandeglang", "postal_code" => "42212"),
      array("city_id" => "332", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Pangandaran", "postal_code" => "46511"),
      array("city_id" => "333", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Pangkajene Kepulauan", "postal_code" => "90611"),
      array("city_id" => "334", "province_id" => "2", "province" => "Bangka Belitung", "type" => "Kota", "city_name" => "Pangkal Pinang", "postal_code" => "33115"),
      array("city_id" => "335", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Paniai", "postal_code" => "98765"),
      array("city_id" => "336", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kota", "city_name" => "Parepare", "postal_code" => "91123"),
      array("city_id" => "337", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Pariaman", "postal_code" => "25511"),
      array("city_id" => "338", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Parigi Moutong", "postal_code" => "94411"),
      array("city_id" => "339", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Pasaman", "postal_code" => "26318"),
      array("city_id" => "340", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Pasaman Barat", "postal_code" => "26511"),
      array("city_id" => "341", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Paser", "postal_code" => "76211"),
      array("city_id" => "342", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Pasuruan", "postal_code" => "67153"),
      array("city_id" => "343", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Pasuruan", "postal_code" => "67118"),
      array("city_id" => "344", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Pati", "postal_code" => "59114"),
      array("city_id" => "345", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Payakumbuh", "postal_code" => "26213"),
      array("city_id" => "346", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Pegunungan Arfak", "postal_code" => "98354"),
      array("city_id" => "347", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Pegunungan Bintang", "postal_code" => "99573"),
      array("city_id" => "348", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Pekalongan", "postal_code" => "51161"),
      array("city_id" => "349", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Pekalongan", "postal_code" => "51122"),
      array("city_id" => "350", "province_id" => "26", "province" => "Riau", "type" => "Kota", "city_name" => "Pekanbaru", "postal_code" => "28112"),
      array("city_id" => "351", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Pelalawan", "postal_code" => "28311"),
      array("city_id" => "352", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Pemalang", "postal_code" => "52319"),
      array("city_id" => "353", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Pematang Siantar", "postal_code" => "21126"),
      array("city_id" => "354", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kabupaten", "city_name" => "Penajam Paser Utara", "postal_code" => "76311"),
      array("city_id" => "355", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Pesawaran", "postal_code" => "35312"),
      array("city_id" => "356", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Pesisir Barat", "postal_code" => "35974"),
      array("city_id" => "357", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Pesisir Selatan", "postal_code" => "25611"),
      array("city_id" => "358", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Pidie", "postal_code" => "24116"),
      array("city_id" => "359", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Pidie Jaya", "postal_code" => "24186"),
      array("city_id" => "360", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Pinrang", "postal_code" => "91251"),
      array("city_id" => "361", "province_id" => "7", "province" => "Gorontalo", "type" => "Kabupaten", "city_name" => "Pohuwato", "postal_code" => "96419"),
      array("city_id" => "362", "province_id" => "27", "province" => "Sulawesi Barat", "type" => "Kabupaten", "city_name" => "Polewali Mandar", "postal_code" => "91311"),
      array("city_id" => "363", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Ponorogo", "postal_code" => "63411"),
      array("city_id" => "364", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Pontianak", "postal_code" => "78971"),
      array("city_id" => "365", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kota", "city_name" => "Pontianak", "postal_code" => "78112"),
      array("city_id" => "366", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Poso", "postal_code" => "94615"),
      array("city_id" => "367", "province_id" => "33", "province" => "Sumatera Selatan", "type" => "Kota", "city_name" => "Prabumulih", "postal_code" => "31121"),
      array("city_id" => "368", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Pringsewu", "postal_code" => "35719"),
      array("city_id" => "369", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Probolinggo", "postal_code" => "67282"),
      array("city_id" => "370", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Probolinggo", "postal_code" => "67215"),
      array("city_id" => "371", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Pulang Pisau", "postal_code" => "74811"),
      array("city_id" => "372", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kabupaten", "city_name" => "Pulau Morotai", "postal_code" => "97771"),
      array("city_id" => "373", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Puncak", "postal_code" => "98981"),
      array("city_id" => "374", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Puncak Jaya", "postal_code" => "98979"),
      array("city_id" => "375", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Purbalingga", "postal_code" => "53312"),
      array("city_id" => "376", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Purwakarta", "postal_code" => "41119"),
      array("city_id" => "377", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Purworejo", "postal_code" => "54111"),
      array("city_id" => "378", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Raja Ampat", "postal_code" => "98489"),
      array("city_id" => "379", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Rejang Lebong", "postal_code" => "39112"),
      array("city_id" => "380", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Rembang", "postal_code" => "59219"),
      array("city_id" => "381", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Rokan Hilir", "postal_code" => "28992"),
      array("city_id" => "382", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Rokan Hulu", "postal_code" => "28511"),
      array("city_id" => "383", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Rote Ndao", "postal_code" => "85982"),
      array("city_id" => "384", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kota", "city_name" => "Sabang", "postal_code" => "23512"),
      array("city_id" => "385", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sabu Raijua", "postal_code" => "85391"),
      array("city_id" => "386", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Salatiga", "postal_code" => "50711"),
      array("city_id" => "387", "province_id" => "15", "province" => "Kalimantan Timur", "type" => "Kota", "city_name" => "Samarinda", "postal_code" => "75133"),
      array("city_id" => "388", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Sambas", "postal_code" => "79453"),
      array("city_id" => "389", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Samosir", "postal_code" => "22392"),
      array("city_id" => "390", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Sampang", "postal_code" => "69219"),
      array("city_id" => "391", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Sanggau", "postal_code" => "78557"),
      array("city_id" => "392", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Sarmi", "postal_code" => "99373"),
      array("city_id" => "393", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Sarolangun", "postal_code" => "37419"),
      array("city_id" => "394", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Sawah Lunto", "postal_code" => "27416"),
      array("city_id" => "395", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Sekadau", "postal_code" => "79583"),
      array("city_id" => "396", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Selayar (Kepulauan Selayar)", "postal_code" => "92812"),
      array("city_id" => "397", "province_id" => "4", "province" => "Bengkulu", "type" => "Kabupaten", "city_name" => "Seluma", "postal_code" => "38811"),
      array("city_id" => "398", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Semarang", "postal_code" => "50511"),
      array("city_id" => "399", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Semarang", "postal_code" => "50135"),
      array("city_id" => "400", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Seram Bagian Barat", "postal_code" => "97561"),
      array("city_id" => "401", "province_id" => "19", "province" => "Maluku", "type" => "Kabupaten", "city_name" => "Seram Bagian Timur", "postal_code" => "97581"),
      array("city_id" => "402", "province_id" => "3", "province" => "Banten", "type" => "Kabupaten", "city_name" => "Serang", "postal_code" => "42182"),
      array("city_id" => "403", "province_id" => "3", "province" => "Banten", "type" => "Kota", "city_name" => "Serang", "postal_code" => "42111"),
      array("city_id" => "404", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Serdang Bedagai", "postal_code" => "20915"),
      array("city_id" => "405", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Seruyan", "postal_code" => "74211"),
      array("city_id" => "406", "province_id" => "26", "province" => "Riau", "type" => "Kabupaten", "city_name" => "Siak", "postal_code" => "28623"),
      array("city_id" => "407", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Sibolga", "postal_code" => "22522"),
      array("city_id" => "408", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Sidenreng Rappang/Rapang", "postal_code" => "91613"),
      array("city_id" => "409", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Sidoarjo", "postal_code" => "61219"),
      array("city_id" => "410", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Sigi", "postal_code" => "94364"),
      array("city_id" => "411", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Sijunjung (Sawah Lunto Sijunjung)", "postal_code" => "27511"),
      array("city_id" => "412", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sikka", "postal_code" => "86121"),
      array("city_id" => "413", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Simalungun", "postal_code" => "21162"),
      array("city_id" => "414", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kabupaten", "city_name" => "Simeulue", "postal_code" => "23891"),
      array("city_id" => "415", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kota", "city_name" => "Singkawang", "postal_code" => "79117"),
      array("city_id" => "416", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Sinjai", "postal_code" => "92615"),
      array("city_id" => "417", "province_id" => "12", "province" => "Kalimantan Barat", "type" => "Kabupaten", "city_name" => "Sintang", "postal_code" => "78619"),
      array("city_id" => "418", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Situbondo", "postal_code" => "68316"),
      array("city_id" => "419", "province_id" => "5", "province" => "DI Yogyakarta", "type" => "Kabupaten", "city_name" => "Sleman", "postal_code" => "55513"),
      array("city_id" => "420", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Solok", "postal_code" => "27365"),
      array("city_id" => "421", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kota", "city_name" => "Solok", "postal_code" => "27315"),
      array("city_id" => "422", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Solok Selatan", "postal_code" => "27779"),
      array("city_id" => "423", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Soppeng", "postal_code" => "90812"),
      array("city_id" => "424", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Sorong", "postal_code" => "98431"),
      array("city_id" => "425", "province_id" => "25", "province" => "Papua Barat", "type" => "Kota", "city_name" => "Sorong", "postal_code" => "98411"),
      array("city_id" => "426", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Sorong Selatan", "postal_code" => "98454"),
      array("city_id" => "427", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Sragen", "postal_code" => "57211"),
      array("city_id" => "428", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Subang", "postal_code" => "41215"),
      array("city_id" => "429", "province_id" => "21", "province" => "Nanggroe Aceh Darussalam (NAD)", "type" => "Kota", "city_name" => "Subulussalam", "postal_code" => "24882"),
      array("city_id" => "430", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Sukabumi", "postal_code" => "43311"),
      array("city_id" => "431", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Sukabumi", "postal_code" => "43114"),
      array("city_id" => "432", "province_id" => "14", "province" => "Kalimantan Tengah", "type" => "Kabupaten", "city_name" => "Sukamara", "postal_code" => "74712"),
      array("city_id" => "433", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Sukoharjo", "postal_code" => "57514"),
      array("city_id" => "434", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sumba Barat", "postal_code" => "87219"),
      array("city_id" => "435", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sumba Barat Daya", "postal_code" => "87453"),
      array("city_id" => "436", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sumba Tengah", "postal_code" => "87358"),
      array("city_id" => "437", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Sumba Timur", "postal_code" => "87112"),
      array("city_id" => "438", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Sumbawa", "postal_code" => "84315"),
      array("city_id" => "439", "province_id" => "22", "province" => "Nusa Tenggara Barat (NTB)", "type" => "Kabupaten", "city_name" => "Sumbawa Barat", "postal_code" => "84419"),
      array("city_id" => "440", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Sumedang", "postal_code" => "45326"),
      array("city_id" => "441", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Sumenep", "postal_code" => "69413"),
      array("city_id" => "442", "province_id" => "8", "province" => "Jambi", "type" => "Kota", "city_name" => "Sungaipenuh", "postal_code" => "37113"),
      array("city_id" => "443", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Supiori", "postal_code" => "98164"),
      array("city_id" => "444", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kota", "city_name" => "Surabaya", "postal_code" => "60119"),
      array("city_id" => "445", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Surakarta (Solo)", "postal_code" => "57113"),
      array("city_id" => "446", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Tabalong", "postal_code" => "71513"),
      array("city_id" => "447", "province_id" => "1", "province" => "Bali", "type" => "Kabupaten", "city_name" => "Tabanan", "postal_code" => "82119"),
      array("city_id" => "448", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Takalar", "postal_code" => "92212"),
      array("city_id" => "449", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Tambrauw", "postal_code" => "98475"),
      array("city_id" => "450", "province_id" => "16", "province" => "Kalimantan Utara", "type" => "Kabupaten", "city_name" => "Tana Tidung", "postal_code" => "77611"),
      array("city_id" => "451", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Tana Toraja", "postal_code" => "91819"),
      array("city_id" => "452", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Tanah Bumbu", "postal_code" => "72211"),
      array("city_id" => "453", "province_id" => "32", "province" => "Sumatera Barat", "type" => "Kabupaten", "city_name" => "Tanah Datar", "postal_code" => "27211"),
      array("city_id" => "454", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Tanah Laut", "postal_code" => "70811"),
      array("city_id" => "455", "province_id" => "3", "province" => "Banten", "type" => "Kabupaten", "city_name" => "Tangerang", "postal_code" => "15914"),
      array("city_id" => "456", "province_id" => "3", "province" => "Banten", "type" => "Kota", "city_name" => "Tangerang", "postal_code" => "15111"),
      array("city_id" => "457", "province_id" => "3", "province" => "Banten", "type" => "Kota", "city_name" => "Tangerang Selatan", "postal_code" => "15332"),
      array("city_id" => "458", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Tanggamus", "postal_code" => "35619"),
      array("city_id" => "459", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Tanjung Balai", "postal_code" => "21321"),
      array("city_id" => "460", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Tanjung Jabung Barat", "postal_code" => "36513"),
      array("city_id" => "461", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Tanjung Jabung Timur", "postal_code" => "36719"),
      array("city_id" => "462", "province_id" => "17", "province" => "Kepulauan Riau", "type" => "Kota", "city_name" => "Tanjung Pinang", "postal_code" => "29111"),
      array("city_id" => "463", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Tapanuli Selatan", "postal_code" => "22742"),
      array("city_id" => "464", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Tapanuli Tengah", "postal_code" => "22611"),
      array("city_id" => "465", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Tapanuli Utara", "postal_code" => "22414"),
      array("city_id" => "466", "province_id" => "13", "province" => "Kalimantan Selatan", "type" => "Kabupaten", "city_name" => "Tapin", "postal_code" => "71119"),
      array("city_id" => "467", "province_id" => "16", "province" => "Kalimantan Utara", "type" => "Kota", "city_name" => "Tarakan", "postal_code" => "77114"),
      array("city_id" => "468", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kabupaten", "city_name" => "Tasikmalaya", "postal_code" => "46411"),
      array("city_id" => "469", "province_id" => "9", "province" => "Jawa Barat", "type" => "Kota", "city_name" => "Tasikmalaya", "postal_code" => "46116"),
      array("city_id" => "470", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kota", "city_name" => "Tebing Tinggi", "postal_code" => "20632"),
      array("city_id" => "471", "province_id" => "8", "province" => "Jambi", "type" => "Kabupaten", "city_name" => "Tebo", "postal_code" => "37519"),
      array("city_id" => "472", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Tegal", "postal_code" => "52419"),
      array("city_id" => "473", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kota", "city_name" => "Tegal", "postal_code" => "52114"),
      array("city_id" => "474", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Teluk Bintuni", "postal_code" => "98551"),
      array("city_id" => "475", "province_id" => "25", "province" => "Papua Barat", "type" => "Kabupaten", "city_name" => "Teluk Wondama", "postal_code" => "98591"),
      array("city_id" => "476", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Temanggung", "postal_code" => "56212"),
      array("city_id" => "477", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kota", "city_name" => "Ternate", "postal_code" => "97714"),
      array("city_id" => "478", "province_id" => "20", "province" => "Maluku Utara", "type" => "Kota", "city_name" => "Tidore Kepulauan", "postal_code" => "97815"),
      array("city_id" => "479", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Timor Tengah Selatan", "postal_code" => "85562"),
      array("city_id" => "480", "province_id" => "23", "province" => "Nusa Tenggara Timur (NTT)", "type" => "Kabupaten", "city_name" => "Timor Tengah Utara", "postal_code" => "85612"),
      array("city_id" => "481", "province_id" => "34", "province" => "Sumatera Utara", "type" => "Kabupaten", "city_name" => "Toba Samosir", "postal_code" => "22316"),
      array("city_id" => "482", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Tojo Una-Una", "postal_code" => "94683"),
      array("city_id" => "483", "province_id" => "29", "province" => "Sulawesi Tengah", "type" => "Kabupaten", "city_name" => "Toli-Toli", "postal_code" => "94542"),
      array("city_id" => "484", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Tolikara", "postal_code" => "99411"),
      array("city_id" => "485", "province_id" => "31", "province" => "Sulawesi Utara", "type" => "Kota", "city_name" => "Tomohon", "postal_code" => "95416"),
      array("city_id" => "486", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Toraja Utara", "postal_code" => "91831"),
      array("city_id" => "487", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Trenggalek", "postal_code" => "66312"),
      array("city_id" => "488", "province_id" => "19", "province" => "Maluku", "type" => "Kota", "city_name" => "Tual", "postal_code" => "97612"),
      array("city_id" => "489", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Tuban", "postal_code" => "62319"),
      array("city_id" => "490", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Tulang Bawang", "postal_code" => "34613"),
      array("city_id" => "491", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Tulang Bawang Barat", "postal_code" => "34419"),
      array("city_id" => "492", "province_id" => "11", "province" => "Jawa Timur", "type" => "Kabupaten", "city_name" => "Tulungagung", "postal_code" => "66212"),
      array("city_id" => "493", "province_id" => "28", "province" => "Sulawesi Selatan", "type" => "Kabupaten", "city_name" => "Wajo", "postal_code" => "90911"),
      array("city_id" => "494", "province_id" => "30", "province" => "Sulawesi Tenggara", "type" => "Kabupaten", "city_name" => "Wakatobi", "postal_code" => "93791"),
      array("city_id" => "495", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Waropen", "postal_code" => "98269"),
      array("city_id" => "496", "province_id" => "18", "province" => "Lampung", "type" => "Kabupaten", "city_name" => "Way Kanan", "postal_code" => "34711"),
      array("city_id" => "497", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Wonogiri", "postal_code" => "57619"),
      array("city_id" => "498", "province_id" => "10", "province" => "Jawa Tengah", "type" => "Kabupaten", "city_name" => "Wonosobo", "postal_code" => "56311"),
      array("city_id" => "499", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Yahukimo", "postal_code" => "99041"),
      array("city_id" => "500", "province_id" => "24", "province" => "Papua", "type" => "Kabupaten", "city_name" => "Yalimo", "postal_code" => "99481"),
      array("city_id" => "501", "province_id" => "5", "province" => "DI Yogyakarta", "type" => "Kota", "city_name" => "Yogyakarta", "postal_code" => "55222")
      );
    }
}