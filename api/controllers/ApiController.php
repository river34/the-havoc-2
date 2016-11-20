<?php
namespace api\controllers;

use Yii;
use common\models\Player;

class ApiController extends \yii\web\Controller
{
    protected $params = array();
    protected $ini_time = 0;
    public $debug = true;
    public $filename = '../web/track.txt';

    public function handshake($key) {
        $player = Player::findOne(['key'=>$key]);
        if (!$player) {
            $player = new Player();
            $player->name = microtime().rand();
            $key = md5(microtime().rand());
            $player->key = $key;
            $player->device = $_SERVER['HTTP_USER_AGENT'];
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $player->ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $player->ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $player->ip = $_SERVER['REMOTE_ADDR'];
            }
            $player->save();
        }
        return $key;
    }

    public function beforeAction($action) {
        date_default_timezone_set('us/eastern');
        $this->enableCsrfValidation = false;
        $request = Yii::$app->request;

        $data = $request->post("data");
        if ($data) {
            //$this->params = json_decode($data, true);
            $this->params = $data;
        } else {
            $data = $request->get("data");
            if ($data) {
                $this->params = json_decode($data, true);
            } else {
                $this->params = $request->get();
                if ($this->params) {
                    foreach ($this->params as $key=>$element) {
                        if ($element) {
                            //
                        } else if (is_array(json_decode($element, true))) {
                            $this->params[$key] = json_decode($element, true);
                        }
                    }
                }
            }
        }

        if ($this->debug && $this->params) {
            try {
                Yii::$app->db->createCommand("INSERT INTO log (log) VALUES ('". json_encode($this->params) ."')")->execute();
            } catch (Exception $e) {
                throw new Exception("Error : ".$e);
            }
        }

        $this->ini_time = microtime(true);

        return parent::beforeAction($action);
    }

    public function afterAction($action, $result) {
        return parent::afterAction($action, $result);
    }
}
?>
