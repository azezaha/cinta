<?php

namespace app\controllers;
use app\models\Kota; //manggil menggunakan model pake use

class CobaController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

	public function actionSembarang()
    {
    	$list=array(13,1,20);
    	 $kota=Kota::find()->where(['id_kota'=>$list])->asArray()->all(); 

    	// $kota=Kota::findOne(100); //pake id
    	// $kota->nama_kota='new york';
    	// $kota->delete();
    	// $kota=new Kota();  //insert data
    	// $kota->nama_kota='london';
    	// $kota->save();


    	$var='asdfghjkl';
        return $this->render('sembarang', ['var'=>$kota]);
    }

    public function actionTarif(){
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
        //looping untuk mencari nilai tarifMax
        for ($i=0; $i < $n; $i++) { 
            if($data[$i]['tarif'] > $tarifMax) {
                $tarifMax = $data[$i]['tarif'];
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
            $durasi = $data[$i]['durasi'];
            
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

            

            echo $murah . " - " . $sedang . " - " . $mahal . "<br/>";
            //yang murah akan masuk ke dataMurah, yang sedang masuk ke dataSedang,
            //yang mahal masuk ke dataMahal
            if ($murah >= $sedang && $murah >= $mahal) {
                $data[$i]['murah'] = $murah;
                array_push($dataMurah, $data[$i]);
            }
            if ($sedang >= $mahal && $sedang >= $murah) {
                $data[$i]['sedang'] = $sedang;
                array_push($dataSedang, $data[$i]);
            }
            if ($mahal >= $murah && $mahal >= $sedang) {
                $data[$i]['mahal'] = $mahal;
                array_push($dataMahal, $data[$i]);
            }
        }

        //sort dataMurah
        if (sizeof($dataMurah) > 1) {
            for ($i=0; $i < sizeof($dataMurah)-1; $i++) { 
                for ($j=$i+1; $j < sizeof($dataMurah) ; $j++) { 
                    if ($dataMurah[$i] < $dataMurah[$j]) {
                        $temp = $dataMurah[$i];
                        $dataMurah[$i] = $dataMurah[$j];
                        $dataMurah[$j] = $temp;
                    }
                }
            }
        }

        //sort dataSedang
        if (sizeof($dataSedang) > 1) {
            for ($i=0; $i < sizeof($dataSedang)-1; $i++) { 
                for ($j=$i+1; $j < sizeof($dataSedang) ; $j++) { 
                    if ($dataSedang[$i] < $dataSedang[$j]) {
                        $temp = $dataSedang[$i];
                        $dataSedang[$i] = $dataSedang[$j];
                        $dataSedang[$j] = $temp;
                    }
                }
            }
        }

        //sort dataMahal
        if (sizeof($dataMahal) > 1) {
            for ($i=0; $i < sizeof($dataMahal)-1; $i++) { 
                for ($j=$i+1; $j < sizeof($dataMahal) ; $j++) { 
                    if ($dataMahal[$i] < $dataMahal[$j]) {
                        $temp = $dataMahal[$i];
                        $dataMahal[$i] = $dataMahal[$j];
                        $dataMahal[$j] = $temp;
                    }
                }
            }
        }

        print_r($dataMurah); echo "<br/>";
        print_r($dataSedang); echo "<br/>";
        print_r($dataMahal);

    }
}


