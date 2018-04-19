<article class="col1">
<!--        <ul class="tabs">
          <li><a href="#" class="active">Flight</a></li>
          <li><a href="#">Hotel</a></li>
          <li><a href="#">Car</a></li>
          <li class="end"><a href="#">Cruise</a></li>
        </ul> -->
        <div class="tabs_cont">
            <div class="bg">
              <!-- <div class="wrapper"> -->
              </div> 
              <!-- <h3> CHECK TARIFF </h3> -->
                  <div class="wrapper">
                  <form action="http://api.rajaongkir.com/starter/cost" method="post">
                    <input type="hidden" name="key" value="b10ec3bb9ddecd2c7060e06aecd2a306"/>
                    <input type="hidden" name="courier" value="jne"/>
                    <table style="color: white">
                        <tr >
                            <td>From</td>
                            <td><select name="origin">
                          <option> -- Choose City -- </option>
                            <?php
                                foreach ($kota as $key) {
                                    ?> 
                                    <option value=<?=$key['city_id']?>><?=$key['city_name']?></option>
                                    <?php
                                }
                         ?>
                      </select></td>
                        </tr>
                        <tr>
                            <td>Destination</td>
                            <td><select name="destination">
                          <option> -- Choose City -- </option>
                            <?php
                                foreach ($kota as $key) {
                                    ?> 
                                    <option value=<?=$key['city_id']?>><?=$key['city_name']?></option>
                                    <?php
                                }
                         ?>
                      </select></td>
                        </tr>
                        <tr>
                            <td>Weight</td>
                            <td><input type="number" class="input" name="weight"></td>
                        </tr>
                      </table>

                      <table style="color: white;">
                        <tr>
                          <td colspan="2" style="text-align: center;">Filter Kriteria</td>
                        </tr>
                        <tr>
                          <td style="text-align: center;"><input type="checkbox" name="filters[]" value="tarif" checked>Tarif</td>
                          <td style="text-align: center;"><input type="checkbox" name="filters[]" value="durasi" checked>Durasi</td>
                        </tr>
                        <tr>
                          <td>
                            <input type="radio" name="tarif" value="murah" checked="checked" />Murah<br/>
                            <input type="radio" name="tarif" value="sedang"/>Sedang<br/>
                            <input type="radio" name="tarif" value="mahal"/>Mahal
                          </td>
                          <td>
                            <input type="radio" name="durasi" value="cepat" checked="checked"/>Cepat<br/>
                            <input type="radio" name="durasi" value="sedang"/>Sedang<br/>
                            <input type="radio" name="durasi" value="lama"/>Lama
                          </td>
                        </tr>
                    </table>
                    <table style="color: white;">
                      <tr>
                          <td>Package</td>
                          <td><select name="Package">
                            <option> --pilih -- </option>
                            <option value="Tidakada">Tidak Ada</option>
                            <option value="Plastik">Plastik</option>
                            <option value="Bubblewrap">Bubble Wrap</option>
                            <option value="Kayu">Packing Kayu</option></select>
                          </td>
                      </tr>
                      <tr>
                        <td colspan="2"><input type="checkbox" name="anjem" value="anjem" checked> Antar-Jemput Paket<br/>*khusus J&T</td>
                      </tr>
                    </tr>
                    </table>

                    <div class="wrapper pad_bot1" style="text-align: center;"> 
                      <button type="submit" class="button">Search</button>
                    </div>
                  </form>
        </div>
      </article>
      <article class="col1 pad_left1">
        <div class="text"> <img src="images/text1.jpg" alt="">
          <h2>Test Fuzzy Surabaya - Jakarta</h2>

          <?php
          
          if(isset($fuzzy)) { 
            if(!empty($fuzzy)) {?>
          <table>
            <tr>
              <th>Ekspedisi</th>
              <th>Jenis</th>
              <th>Tarif</th>
              <th colspan="2">Kategori</th>
            </tr>
            <?php
              foreach ($fuzzy as $data) { ?>
                <tr>
                  <td><?=$data['ekspedisi']?></td>
                  <td><?=$data['jenis']?></td>
                  <td><?=$data['tarif']?></td>
                  <td><?php
                  if(isset($data['tarif_murah'])) echo "Murah";
                  elseif(isset($data['tarif_sedang'])) echo "Sedang";
                  else echo "Mahal";
                  ?></td>
                  <td><?php
                  if(isset($data['durasi_lama'])) echo "Lama";
                  elseif(isset($data['durasi_sedang'])) echo "Sedang";
                  else echo "Cepat";
                  ?></td>
                </tr>
              <?php } ?>
            </table >
            <?php
              } else {
                echo "<h5>Hasil tidak ada</h5>";
              }
            }
          if(isset($alert))
            echo "<h5>".$alert."</h5>";

          ?>
          
      </article>
      <!-- <div class="img"><img src="<?=Yii::$app->homeUrl?>/images/img.jpg" alt=""></div> -->