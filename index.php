<?php
//ini_set('display_errors',1);
ini_set('error_log','php.log');
session_start();
?>




<?php


$enemys = array();


//抽象クラス　ヒソカと敵の大枠

abstract class Hunter{
  protected $name;
  protected $hp;
  protected $img;
  protected $attackMin;
  protected $attackMax;
  protected $voice1;
  abstract public function damagedVoice();
  //名前のセッター
  public function setName($str){
    $this->name = $str;
    }
  public function getName(){
    return $this->name;
  }
  //HPのセッター
  public function setHP($num){
    $this->hp = $num;
  }
  public function getHp(){
    return $this->hp;
  }
  public function getImg(){
    return $this->img;
  }
  public function attack($target){
    $attackPoint = mt_rand($this->attackMin,$this->attackMax);
    if(!mt_rand(0,4)){
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->voice1);
      }
    $target->setHp($target->getHp() - $attackPoint);
    History::set($attackPoint.'のダメージ');
  }
}

//ヒソカのクラス　ヒソカ以外にしたくなった場合ここを変える

class Hero extends Hunter{
  //プロパティ
  protected $voice2;
  protected $voice3;

    //コンストラクト
    public function __construct($name,$hp,$img,$attackMin,$attackMax,$voice1,$voice2,$voice3){
    $this->name =$name;
    $this->hp = $hp;
    $this->img =$img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->voice1 = $voice1;
    $this->voice2 = $voice2;
    $this->voice3 = $voice3;
  }
  //ゲッター

  //回復する時
  public function cure($target){
  $curePoint = mt_rand(200,250);
  History::set($this->voice2);
  $target->setHp($target->getHp() + $curePoint);
  History::set('腕が繋がる！？'.$curePoint.'の回復！！');
  }
  //必殺技
  public function specialAttack($target){
  $attackPoint = mt_rand($this->attackMin,$this->attackMax);
  $attackPoint = 1000;
  History::set($this->voice3);
  $target->setHp($target->getHp() - $attackPoint);
  History::set($attackPoint.'ポイントのダメージ');
  }
  
  //ヒソカがダメージを受けた時
  public function damagedVoice(){
    History::set($this->name.'が体をくねらせながら叫ぶ！');
    History::set('もっと楽しませてよ★');
  }
  
  }

//敵クラス　敵の設計図
class Enemy extends Hunter{
  //プロパティ
  protected $voice2;
  protected $voice3;
  //コンストラクト
  public function __construct($name,$hp,$img,$attackMin,$attackMax,$voice1,$voice2){
    $this->name =$name;
    $this->hp = $hp;
    $this->img =$img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->voice1 = $voice1;
    $this->voice2 = $voice2;
  }
  
  public function getVoice2(){
    return $this->voice2;
  }
  //相手がダメージを受けた時
  public function damagedVoice(){
    History::set($this->name.'が叫ぶ');
    $damage_voice = array('ぐはっ！','のはっ！','がはぁ');
    $key = array_rand($damage_voice);
    History::set($damage_voice[$key]);
  }
}

  




class History {
  //メッセージを管理するクラスで静的クラスで表現
  public static function set($str){
    if(empty($_SESSION['history'])) $_SESSION['history'] = "";
    
    $_SESSION['history'] .= $str.'<br>';
  }
  //静的メソッドのヒストリー全消し
  public static function clear(){
    unset($_SESSION['history']);
  }
}

//インスタンス生成
$hero = new Hero('ヒソカ' , 1000 , 'img/prof.png' ,200 , 300,'バンジーガム（伸縮自在の愛）♠️','ドッキリテクスチャー（嘘っぺらな愛）♣️','ギュイーーーーーーーン。来てるね★');
  
$enemys[] = new Enemy('カストロ',1000,'img/kastro.png',100,150,'ダブル！！','容量の無駄遣い❤️');
$enemys[] = new Enemy('ゴトー',1200,'img/goto.png',150,200,'後悔するなよ似非マジシャン！！！','答えは...<br>お終い★');
$enemys[] = new Enemy('クロロ',1500,'img/kuroro.png',170,200,'スキルハンター！','やっぱり団長は強いや♣️');

function createEnemy(){
    if($_SESSION['knockDownCount'] <=2){
     global $enemys;
     $enemy =  $enemys[$_SESSION['knockDownCount']];      
     History::set($enemy->getName().'が襲いかかってくる！！');
     $_SESSION['enemy'] = $enemy;
    }
}
 
 function createHero(){
  global $hero;
  $_SESSION['hero'] = $hero;
}

 function init(){
  History::clear();
  History::set('これはヒソカの遥か昔の<br>戦いの記憶');
  $_SESSION['knockDownCount'] = 0;
  createEnemy();
  createHero();
}

function gameOver(){
  $_SESSION = array();
}

  
  //ゲーム開始


  //POST送信されていた場合
  if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack']))? true : false;
    $startFlg = (!empty($_POST['start']))? true : false;
    $cureFlg = (!empty($_POST['cure']))? true: false;
    $specialFlg = (!empty($_POST['special']))? true : false;
    $restartFlg = (!empty($_POST['restart']))? true : false;
    $escapeFlg = (!empty($_POST['escape']))? true : false;
    $lastFlg = (!empty($_POST['last']))? true: false;
    error_log('POSTされた');
  }
//    var_dump($specialFlg);
//    var_dump($startFlg);
//    var_dump($escapeFlg);
//    var_dump($attackFlg);
    
  
  if($startFlg){
    History::set('ゲームスタート');
    init();
  }

    
     if($cureFlg){
      //回復を押した場合
      $_SESSION['history'] = "";
      History::set('無駄な努力ご苦労様');
      $_SESSION['hero']->cure($_SESSION['hero']);
     }
    
      if($specialFlg){
      $_SESSION['history'] = "";
      $_SESSION['hero']->specialAttack($_SESSION['enemy']);
      }
     //攻撃をするを選択した場合
      if($escapeFlg){
      $_SESSION['history'] = "";
      $_SESSION['history'] .= '僕は逃げるのが嫌いなのさ♠︎';
      }
    
     if($attackFlg){
      //相手に攻撃を与える
      $_SESSION['history'] = "";
      History::set($_SESSION['hero']->getName().'の攻撃');
      $_SESSION['hero']->attack($_SESSION['enemy']);
      $_SESSION['enemy']->damagedVoice();
     }
          //相手に体力が残っていれば　相手からの攻撃
      if(($cureFlg || $specialFlg || $attackFlg) && $_SESSION['enemy']->getHp() > 0){
        History::set($_SESSION['enemy']->getName().'の攻撃');
        $_SESSION['enemy']->attack($_SESSION['hero']);
        $_SESSION['hero']->damagedVoice();
        }
      
      if(!empty($_SESSION['enemy']) && $_SESSION['enemy']->getHp() <= 0){
        //相手の体力が0以下であれば別の敵を出現させる
        History::set($_SESSION['enemy']->getName().'を倒した');
        History::set($_SESSION['enemy']->getVoice2());
        
        if(count($enemys) > $_SESSION['knockDownCount']){
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        if($_SESSION['knockDownCount'] <=2){
        createEnemy();
        }
        }

        

        //自分の体力が0以下になった場合リセット
        if(!empty($_SESSION['enemy']) && $_SESSION['hero']->getHp() <= 0) {
          gameOver();
        }
      }

      
    //三体倒したあと
  if($lastFlg){
    gameOver();
  }
    $_POST = array();
  
  
?>


<!DOCTYPE html>

<html>
<meta charsert=UTF-8>

<head>
  <title>Hunter✖︎Hunter</title>
  <link rel="stylesheet" href="style.css">
</head>
<div class="container">
  <h1 style=" margin:3% 0;">ヒソカの冒険</h1>
  <?php if(empty($_SESSION)) {
      ?>
  <div style="position:relative;" class="start">
    <h2 style="color: red; padding-top:20%;">❤︎ヒソカの追想❤︎</h2>
    <form method="post">
      <input style="font-size: 30px; margin-top: 16%; border:double 3px red;" type="submit" name="start" value="冒険スタート">
    </form>
  </div>

  <?php }else if($_SESSION['knockDownCount'] ==3){ ?>

  <img src="img/excite.jpeg" alt="last_img">
  <p>ヒソカの狂人じみたストーリーいかがでしたでしょうか？？</p>
  <p>アニメや漫画で見ていただけるとより一層</p>
  <p>不気味さ、面白さがお分かりいただけるかと思います</p>
  <div href="https://www.youtube.com/results?search_query=hunter%C3%97hunter">youtubeで見る！！</div>
  <p>最新話では船内で旅団との死闘も始まりそう！！</p>
  <p>だーいし的ナンバーワンの冒険漫画です</p>
  <p>今後のヒソカの動向にも注目です。</p>
  <form method="post">
    <input type="submit" name="last" value="HOMEに戻る">
  </form>
  <?php
    } else {
      ?>
  <div class="screen"><img class="enemy_img" src="<?php echo $_SESSION['enemy']->getImg() ;?>" alt="enemy-img">
    <div class="enemyHp"><?php echo $_SESSION['enemy']->getName().'のHP：'.$_SESSION['enemy']->getHp();
      ?></div>
    <div class="history"><?php echo $_SESSION['history'];
      ?></div>
  </div>
  <div class="under">
    <div class="img"><img src="<?php echo $_SESSION['hero']->getImg() ;?>" alt="hero-img"></div>
    <div><?php echo $_SESSION['hero']->getName().'のHP：'.$_SESSION['hero']->getHp();
      ?></div>
    <div class="command">
      <form method="post">
        <input type="submit" name="attack" value="攻撃する">
        <input type="submit" name="cure" value="回復する">
        <input type="submit" name="special" value="興奮してきちゃった❤️"><input type="submit" name="escape" value="　逃げる">
        <input type="submit" name="start" value="リスタート">
      </form>
    </div>
  </div>
</div>
<?php } ?>

</html>
